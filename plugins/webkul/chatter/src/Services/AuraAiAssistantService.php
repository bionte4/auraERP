<?php

namespace Webkul\Chatter\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Throwable;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Models\AttendanceSite;
use Webkul\Chatter\Models\Message;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\Employee;
use Webkul\Evaluation\Models\Evaluation;
use Webkul\Performance\Models\PerformancePlan;
use Webkul\PluginManager\Package;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Security\Models\User;

class AuraAiAssistantService
{
    public function answer(string $question, ?Model $record = null): string
    {
        $cleanQuestion = trim(strip_tags($question));
        $cleanQuestion = trim((string) preg_replace('/^@ai\b[:\s,-]*/i', '', $cleanQuestion));

        if ($cleanQuestion === '' && $record !== null) {
            return $this->summarizeRecord($record);
        }

        if ($cleanQuestion === '') {
            return $this->helpSummary();
        }

        $contextSummary = $record !== null
            ? $this->summarizeRecord($record)
            : $this->buildErpInsights($cleanQuestion);

        if ($llmReply = $this->tryExternalLlm($cleanQuestion, $contextSummary, $record)) {
            return $llmReply;
        }

        return $contextSummary;
    }

    public function replyOnRecord(Model $record, string $prompt): ?Message
    {
        if (! method_exists($record, 'addMessage')) {
            return null;
        }

        $answerHtml = $this->formatAsHtml($this->answer($prompt, $record));

        return $record->addMessage([
            'type'        => 'note',
            'name'        => $record->name ?? $record->title ?? class_basename($record),
            'subject'     => __('chatter::ai.chatter.reply-subject'),
            'body'        => $answerHtml,
            'is_internal' => true,
            'properties'  => [
                'ai_assistant' => true,
            ],
        ]);
    }

    public function summarizeRecord(Model $record): string
    {
        $lines = [];
        $type = class_basename($record);
        $title = $record->name ?? $record->title ?? ('#'.$record->getKey());

        $lines[] = __('chatter::ai.summary.header', ['type' => $type, 'title' => $title]);

        if ($record instanceof Employee) {
            $lines = array_merge($lines, $this->employeeRecordDetails($record));
        } else {
            foreach (['status', 'state', 'stage_id', 'work_date', 'period_start', 'period_end'] as $field) {
                if (isset($record->{$field}) && filled($record->{$field})) {
                    $val = is_object($record->{$field}) && property_exists($record->{$field}, 'value')
                        ? $record->{$field}->value
                        : (string) $record->{$field};
                    $lines[] = '• '.ucfirst(str_replace('_', ' ', $field)).': '.$val;
                }
            }
        }

        if (method_exists($record, 'messages')) {
            $recentMessages = $record->messages()
                ->whereIn('type', ['comment', 'note', 'activity'])
                ->latest()
                ->limit(3)
                ->get();

            if ($recentMessages->isNotEmpty()) {
                $lines[] = '';
                $lines[] = __('chatter::ai.summary.recent-chatter');

                foreach ($recentMessages as $msg) {
                    $snippet = trim(strip_tags((string) $msg->body));
                    if ($snippet !== '') {
                        $lines[] = '• '.str($snippet)->limit(100);
                    }
                }
            }
        }

        return implode("\n", $lines);
    }

    /**
     * @return array<int, string>
     */
    protected function employeeRecordDetails(Employee $employee): array
    {
        $lines = [];

        if ($employee->job_title) {
            $lines[] = '• '.__('chatter::ai.employee.job', ['job' => $employee->job_title]);
        }

        if ($employee->department?->name) {
            $lines[] = '• '.__('chatter::ai.employee.department', ['department' => $employee->department->name]);
        }

        if ($employee->employmentType?->name) {
            $lines[] = '• '.__('chatter::ai.employee.type', ['type' => $employee->employmentType->name]);
        }

        if ($employee->join_date) {
            $lines[] = '• '.__('chatter::ai.employee.join-date', ['date' => $employee->join_date->format('d M Y')]);
        }

        if ($employee->contract_end_date) {
            $days = $employee->daysUntilContractEnds();
            $lines[] = '• '.__('chatter::ai.employee.contract-end', [
                'date' => $employee->contract_end_date->format('d M Y'),
                'days' => $days ?? 0,
            ]);
        }

        if (Package::isPluginInstalled('attendances') && Schema::hasTable('attendances_attendances')) {
            $monthCount = Attendance::withoutGlobalScopes()
                ->where('employee_id', $employee->id)
                ->whereMonth('work_date', now()->month)
                ->whereYear('work_date', now()->year)
                ->count();

            $lateCount = Attendance::withoutGlobalScopes()
                ->where('employee_id', $employee->id)
                ->whereMonth('work_date', now()->month)
                ->whereYear('work_date', now()->year)
                ->where('status', 'late')
                ->count();

            $lines[] = '• '.__('chatter::ai.employee.attendance-month', [
                'present' => $monthCount,
                'late'    => $lateCount,
            ]);
        }

        if (Package::isPluginInstalled('performance') && Schema::hasTable('performance_plans')) {
            $plan = PerformancePlan::withoutGlobalScopes()
                ->with('goals')
                ->where('employee_id', $employee->id)
                ->latest('period_start')
                ->first();

            if ($plan) {
                $score = $plan->weightedScore();
                $lines[] = '• '.__('chatter::ai.employee.performance-plan', [
                    'title' => $plan->title,
                    'score' => $score !== null ? number_format($score, 1).'%' : '—',
                ]);
            }
        }

        if (Package::isPluginInstalled('evaluations') && Schema::hasTable('evaluations_evaluations')) {
            $evaluation = Evaluation::withoutGlobalScopes()
                ->with('ratings')
                ->where('employee_id', $employee->id)
                ->latest('period_start')
                ->first();

            if ($evaluation) {
                $score = $evaluation->finalScore();
                $lines[] = '• '.__('chatter::ai.employee.evaluation', [
                    'title' => $evaluation->title,
                    'score' => $score !== null ? number_format($score, 2).' / 5' : '—',
                ]);
            }
        }

        return $lines;
    }

    protected function buildErpInsights(string $question): string
    {
        if (! $this->canViewCompanyWideData()) {
            return $this->buildPersonalInsights($question);
        }

        $q = mb_strtolower($question);

        if (preg_match('/(kontrak|contract|pkwt|habis|berakhir|expir|freelance|konsultan|permanent)/u', $q)) {
            return $this->contractAndTypeInsights();
        }

        if (preg_match('/(absen|hadir|telat|terlambat|late|attendance|clock|lokasi kerja|site|geofence)/u', $q)) {
            return $this->attendanceInsights();
        }

        if (preg_match('/(kinerja|performance|evaluasi|evaluation|kpi|nilai|skor|score|target)/u', $q)) {
            return $this->performanceAndEvaluationInsights();
        }

        if (preg_match('/(proyek|project|task|tugas)/u', $q)) {
            return $this->projectInsights();
        }

        if (preg_match('/(karyawan|employee|departemen|department|staf|staff|hr)/u', $q)) {
            return $this->employeeOverviewInsights();
        }

        return $this->generalOverviewInsights();
    }

    public function canViewCompanyWideData(?User $user = null): bool
    {
        /** @var ?User $authUser */
        $authUser = $user ?? Auth::user();

        if (! $authUser) {
            return false;
        }

        if (method_exists($authUser, 'hasAnyRole') && $authUser->hasAnyRole(['Admin', 'HRD'])) {
            return true;
        }

        return $authUser->can('view_any_employee_employee')
            || $authUser->can('update_employee_employee');
    }

    public function resolveOwnEmployee(?User $user = null): ?Employee
    {
        /** @var ?User $authUser */
        $authUser = $user ?? Auth::user();

        if (! $authUser || ! Schema::hasTable('employees_employees')) {
            return null;
        }

        return Employee::withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->where('user_id', $authUser->id)
            ->first();
    }

    protected function buildPersonalInsights(string $question): string
    {
        $employee = $this->resolveOwnEmployee();

        if (! $employee) {
            $lines = [__('chatter::ai.insights.personal-unlinked')];

            if (Package::isPluginInstalled('attendances') && Schema::hasTable('attendances_sites')) {
                $sites = AttendanceSite::withoutGlobalScopes()->where('is_active', true)->pluck('name');

                if ($sites->isNotEmpty()) {
                    $lines[] = __('chatter::ai.insights.active-sites', ['sites' => $sites->join(', ')]);
                }
            }

            return implode("\n", $lines);
        }

        $lines = [
            __('chatter::ai.insights.personal-heading', ['name' => $employee->name]),
            ...$this->employeeRecordDetails($employee),
        ];

        $q = mb_strtolower($question);

        if (preg_match('/(absen|hadir|telat|terlambat|late|attendance|clock|lokasi kerja|site|geofence)/u', $q)
            && Package::isPluginInstalled('attendances')
            && Schema::hasTable('attendances_attendances')
        ) {
            $todayRecord = Attendance::withoutGlobalScopes()
                ->with('checkInSite')
                ->where('employee_id', $employee->id)
                ->whereDate('work_date', now()->toDateString())
                ->latest('check_in')
                ->first();

            if ($todayRecord) {
                $inTime = $todayRecord->check_in ? $todayRecord->check_in->format('H:i') : '—';
                $outTime = $todayRecord->check_out ? $todayRecord->check_out->format('H:i') : '—';
                $site = $todayRecord->checkInSite?->name ? ' @ '.$todayRecord->checkInSite->name : '';
                $lines[] = '• '.__('chatter::ai.insights.personal-today-attendance', [
                    'in'   => $inTime,
                    'out'  => $outTime,
                    'site' => $site,
                ]);
            }

            if (Schema::hasTable('attendances_sites')) {
                $sites = AttendanceSite::withoutGlobalScopes()->where('is_active', true)->pluck('name');

                if ($sites->isNotEmpty()) {
                    $lines[] = '• '.__('chatter::ai.insights.active-sites', ['sites' => $sites->join(', ')]);
                }
            }
        }

        return implode("\n", $lines);
    }

    protected function contractAndTypeInsights(): string
    {
        if (! Schema::hasTable('employees_employees')) {
            return __('chatter::ai.insights.no-employee-module');
        }

        $lines = [__('chatter::ai.insights.contract-heading')];

        if (Schema::hasColumn('employees_employees', 'contract_end_date')) {
            $expiring = Employee::withoutGlobalScopes()
                ->whereNull('deleted_at')
                ->where('is_active', true)
                ->whereNotNull('contract_end_date')
                ->whereDate('contract_end_date', '<=', now()->addDays(30)->toDateString())
                ->orderBy('contract_end_date')
                ->limit(10)
                ->get();

            if ($expiring->isEmpty()) {
                $lines[] = '• '.__('chatter::ai.insights.no-expiring-contracts');
            } else {
                foreach ($expiring as $emp) {
                    $days = $emp->daysUntilContractEnds() ?? 0;
                    $lines[] = '• '.$emp->name.' — '.$emp->contract_end_date->format('d M Y').' ('.($days < 0 ? __('chatter::ai.insights.expired') : $days.' '.__('chatter::ai.insights.days-left')).')';
                }
            }
        }

        if (Schema::hasColumn('employees_employees', 'employment_type_id')) {
            $byType = Employee::withoutGlobalScopes()
                ->whereNull('deleted_at')
                ->where('is_active', true)
                ->with('employmentType')
                ->get()
                ->groupBy(fn (Employee $e): string => $e->employmentType?->name ?? __('chatter::ai.insights.unassigned'));

            if ($byType->isNotEmpty()) {
                $lines[] = '';
                $lines[] = __('chatter::ai.insights.type-breakdown');

                foreach ($byType as $typeName => $group) {
                    $lines[] = '• '.$typeName.': '.$group->count();
                }
            }
        }

        return implode("\n", $lines);
    }

    protected function attendanceInsights(): string
    {
        if (! Package::isPluginInstalled('attendances') || ! Schema::hasTable('attendances_attendances')) {
            return __('chatter::ai.insights.no-attendance-module');
        }

        $today = now()->toDateString();
        $todayRecords = Attendance::withoutGlobalScopes()
            ->with(['employee', 'checkInSite'])
            ->whereDate('work_date', $today)
            ->get();

        $late = $todayRecords->where('status.value', 'late')->count()
            ?: $todayRecords->filter(fn ($a): bool => (is_object($a->status) ? $a->status->value : $a->status) === 'late')->count();

        $lines = [
            __('chatter::ai.insights.attendance-today', [
                'date'  => now()->format('d M Y'),
                'total' => $todayRecords->count(),
                'late'  => $late,
            ]),
        ];

        foreach ($todayRecords->take(8) as $att) {
            $empName = $att->employee?->name ?? ('#'.$att->employee_id);
            $inTime = $att->check_in ? $att->check_in->format('H:i') : '—';
            $outTime = $att->check_out ? $att->check_out->format('H:i') : '—';
            $site = $att->checkInSite?->name ? ' @ '.$att->checkInSite->name : '';
            $lines[] = "• {$empName}: {$inTime} → {$outTime}{$site}";
        }

        if (Schema::hasTable('attendances_sites')) {
            $sites = AttendanceSite::withoutGlobalScopes()->where('is_active', true)->pluck('name');

            if ($sites->isNotEmpty()) {
                $lines[] = '';
                $lines[] = __('chatter::ai.insights.active-sites', ['sites' => $sites->join(', ')]);
            }
        }

        return implode("\n", $lines);
    }

    protected function performanceAndEvaluationInsights(): string
    {
        $lines = [__('chatter::ai.insights.performance-heading')];

        if (Package::isPluginInstalled('performance') && Schema::hasTable('performance_plans')) {
            $plans = PerformancePlan::withoutGlobalScopes()->with(['employee', 'goals'])->latest()->limit(5)->get();
            $lines[] = '• '.__('chatter::ai.insights.plans-count', ['count' => PerformancePlan::withoutGlobalScopes()->count()]);

            foreach ($plans as $plan) {
                $score = $plan->weightedScore();
                $lines[] = '  - '.($plan->employee?->name ?? '—').': '.$plan->title.' ('.($score !== null ? number_format($score, 1).'%' : '0%').')';
            }
        }

        if (Package::isPluginInstalled('evaluations') && Schema::hasTable('evaluations_evaluations')) {
            $evals = Evaluation::withoutGlobalScopes()->with(['employee', 'ratings'])->latest()->limit(5)->get();
            $lines[] = '• '.__('chatter::ai.insights.evaluations-count', ['count' => Evaluation::withoutGlobalScopes()->count()]);

            foreach ($evals as $eval) {
                $score = $eval->finalScore();
                $lines[] = '  - '.($eval->employee?->name ?? '—').': '.$eval->title.' ('.($score !== null ? number_format($score, 2).'/5' : '—').')';
            }
        }

        return implode("\n", $lines);
    }

    protected function projectInsights(): string
    {
        if (! Schema::hasTable('projects_projects')) {
            return __('chatter::ai.insights.no-project-module');
        }

        $projectCount = Project::withoutGlobalScopes()->whereNull('deleted_at')->count();
        $taskCount = Schema::hasTable('projects_tasks')
            ? Task::withoutGlobalScopes()->whereNull('deleted_at')->count()
            : 0;

        return __('chatter::ai.insights.projects-summary', [
            'projects' => $projectCount,
            'tasks'    => $taskCount,
        ]);
    }

    protected function employeeOverviewInsights(): string
    {
        if (! Schema::hasTable('employees_employees')) {
            return __('chatter::ai.insights.no-employee-module');
        }

        $activeCount = Employee::withoutGlobalScopes()->whereNull('deleted_at')->where('is_active', true)->count();
        $deptCount = Schema::hasTable('employees_departments')
            ? Department::withoutGlobalScopes()->whereNull('deleted_at')->count()
            : 0;

        $lines = [
            __('chatter::ai.insights.employee-summary', [
                'employees'   => $activeCount,
                'departments' => $deptCount,
            ]),
        ];

        $contractSummary = $this->contractAndTypeInsights();
        $lines[] = '';
        $lines[] = $contractSummary;

        return implode("\n", $lines);
    }

    protected function generalOverviewInsights(): string
    {
        $sections = [
            $this->employeeOverviewInsights(),
            $this->attendanceInsights(),
            $this->performanceAndEvaluationInsights(),
        ];

        return implode("\n\n", array_filter($sections));
    }

    protected function helpSummary(): string
    {
        return __('chatter::ai.help');
    }

    protected function tryExternalLlm(string $question, string $erpContext, ?Model $record = null): ?string
    {
        $apiKey = (string) config('services.openai.key', '');

        if ($apiKey === '') {
            return null;
        }

        $endpoint = (string) config('services.openai.endpoint', 'https://api.openai.com/v1/chat/completions');
        $model = (string) config('services.openai.model', 'gpt-4o-mini');

        try {
            $response = Http::withToken($apiKey)
                ->timeout(15)
                ->post($endpoint, [
                    'model'    => $model,
                    'messages' => [
                        [
                            'role'    => 'system',
                            'content' => 'You are auraAI Assistant inside auraERP. Answer concisely in the same language as the user (Indonesian or English) using the provided live ERP context.',
                        ],
                        [
                            'role'    => 'user',
                            'content' => "Konteks ERP:\n{$erpContext}\n\nPertanyaan: {$question}",
                        ],
                    ],
                ]);

            if ($response->successful()) {
                $content = data_get($response->json(), 'choices.0.message.content');

                if (is_string($content) && trim($content) !== '') {
                    return trim($content);
                }
            }
        } catch (Throwable) {
            return null;
        }

        return null;
    }

    protected function formatAsHtml(string $text): string
    {
        $escaped = e($text);

        return '<p><strong>🤖 auraAI Assistant</strong></p><p>'.nl2br($escaped).'</p>';
    }
}
