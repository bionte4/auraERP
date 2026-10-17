<?php

namespace Webkul\Project\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Models\Milestone;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Security\Models\User;
use Webkul\Security\Support\AdminAccess;

class CustomerProjectWeeklyReportData
{
    public function __construct(
        protected OperationalScope $scope,
    ) {}

    public function viewerMayAccess(?User $user = null): bool
    {
        $user ??= auth()->user();

        if (! $user instanceof User || ! method_exists($user, 'hasRole')) {
            return false;
        }

        return AdminAccess::check($user) || $user->hasRole('Operation Manager');
    }

    public function canViewProject(Project $project, ?User $user = null): bool
    {
        $user ??= auth()->user();

        if (! $user instanceof User || ! $this->viewerMayAccess($user)) {
            return false;
        }

        if ($this->scope->isUnscoped($user) || AdminAccess::check($user)) {
            return true;
        }

        return in_array((int) $project->id, $this->scope->projectIds($user), true);
    }

    /**
     * @return array<int|string, string>
     */
    public function projectOptions(?User $user = null): array
    {
        $user ??= auth()->user();
        $query = Project::query()
            ->with('partner')
            ->where('is_active', true)
            ->orderBy('name');

        if (! AdminAccess::check($user) && ! $this->scope->isUnscoped($user)) {
            $ids = $this->scope->projectIds($user);

            if ($ids === []) {
                return [];
            }

            $query->whereIn('id', $ids);
        }

        return $query->get(['id', 'name', 'partner_id'])
            ->mapWithKeys(function (Project $project): array {
                $customer = $project->partner?->name;
                $label = $customer
                    ? "{$project->name} — {$customer}"
                    : $project->name;

                return [$project->id => $label];
            })
            ->all();
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    public function resolvePeriod(mixed $startDate = null, mixed $endDate = null): array
    {
        $start = filled($startDate)
            ? Carbon::parse($startDate)->startOfDay()
            : now()->startOfWeek()->startOfDay();
        $end = filled($endDate)
            ? Carbon::parse($endDate)->endOfDay()
            : ($startDate
                ? $start->copy()->endOfDay()
                : now()->endOfWeek()->endOfDay());

        if ($start->gt($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        return [$start, $end];
    }

    /**
     * @return array{
     *     cover: array<string, string>,
     *     week_label: string,
     *     metrics: array<int, array{key: string, metric: string, value: string|int, notes: string}>,
     *     completed_rows: array<int, array{item: string, status: string, date: string}>,
     *     open_rows: array<int, array{item: string, status: string, date: string}>,
     *     checkpoint_rows: array<int, array{item: string, status: string, date: string}>,
     *     customer_notes: string
     * }
     */
    public function build(
        Project $project,
        ?User $user = null,
        string $customerNotes = '',
        mixed $startDate = null,
        mixed $endDate = null,
    ): array {
        $user ??= auth()->user();
        abort_unless($this->canViewProject($project, $user), 403);

        $indonesian = str_starts_with((string) app()->getLocale(), 'id');
        [$start, $end] = $this->resolvePeriod($startDate, $endDate);

        $tasks = Task::query()
            ->where('project_id', $project->id)
            ->whereNotIn('state', [TaskState::CANCELLED->value])
            ->get(['id', 'state', 'deadline', 'updated_at']);

        $doneStates = [TaskState::DONE->value, TaskState::APPROVED->value];
        $total = $tasks->count();
        $done = $tasks->filter(fn (Task $task): bool => in_array(
            $task->state instanceof TaskState ? $task->state->value : (string) $task->state,
            $doneStates,
            true,
        ))->count();

        $progress = $total > 0 ? (int) round(($done / $total) * 100) : 0;

        $completedInPeriod = $tasks
            ->filter(function (Task $task) use ($doneStates, $start, $end): bool {
                $state = $task->state instanceof TaskState ? $task->state->value : (string) $task->state;

                if (! in_array($state, $doneStates, true)) {
                    return false;
                }

                $updated = $task->updated_at;

                return $updated !== null && $updated->gte($start) && $updated->lte($end);
            })
            ->sortBy('updated_at')
            ->values();

        $openTasks = $tasks
            ->filter(function (Task $task) use ($doneStates): bool {
                $state = $task->state instanceof TaskState ? $task->state->value : (string) $task->state;

                return ! in_array($state, $doneStates, true);
            })
            ->sortBy(fn (Task $task) => $task->deadline?->timestamp ?? PHP_INT_MAX)
            ->values();

        $needsAttention = $openTasks
            ->filter(fn (Task $task): bool => $task->deadline !== null && $task->deadline->lt($end))
            ->count();

        $checkpoints = $this->upcomingCheckpoints($project, $start, $end);

        $periodLabel = $indonesian
            ? $start->translatedFormat('d M Y').' – '.$end->translatedFormat('d M Y')
            : $start->format('M j, Y').' – '.$end->format('M j, Y');

        return [
            'cover' => [
                'customer' => $project->partner?->name
                    ?? ($indonesian ? 'Pelanggan belum diisi' : 'Customer not set'),
                'project'  => $project->name,
                'manager'  => $project->user?->name
                    ?? ($indonesian ? '—' : '—'),
                'period'       => $periodLabel,
                'generated_at' => now()->timezone(config('app.timezone'))->format('Y-m-d H:i'),
            ],
            'week_label' => ($indonesian ? 'Periode ' : 'Period ').$periodLabel,
            'metrics'    => [
                [
                    'key'    => 'progress',
                    'metric' => $indonesian ? 'Progres keseluruhan' : 'Overall progress',
                    'value'  => $progress.'%',
                    'notes'  => $indonesian
                        ? "{$done} dari {$total} deliverable selesai"
                        : "{$done} of {$total} deliverables completed",
                ],
                [
                    'key'    => 'completed_week',
                    'metric' => $indonesian ? 'Selesai pada periode' : 'Completed in period',
                    'value'  => $completedInPeriod->count(),
                    'notes'  => $indonesian ? 'Deliverable yang diselesaikan pada rentang tanggal laporan' : 'Deliverables finished within the selected dates',
                ],
                [
                    'key'    => 'in_progress',
                    'metric' => $indonesian ? 'Sedang berjalan' : 'In progress',
                    'value'  => $openTasks->count(),
                    'notes'  => $indonesian ? 'Deliverable yang masih dikerjakan' : 'Deliverables still being worked on',
                ],
                [
                    'key'    => 'needs_attention',
                    'metric' => $indonesian ? 'Perlu perhatian' : 'Needs attention',
                    'value'  => $needsAttention,
                    'notes'  => $indonesian
                        ? 'Item yang memerlukan tindak lanjut bersama (detail disamarkan)'
                        : 'Items that need joint follow-up (details masked)',
                ],
                [
                    'key'    => 'checkpoints',
                    'metric' => $indonesian ? 'Checkpoint pada / setelah periode' : 'Checkpoints in / after period',
                    'value'  => $checkpoints->count(),
                    'notes'  => $indonesian
                        ? 'Tonggak jadwal dalam periode hingga +14 hari (nama disamarkan)'
                        : 'Schedule checkpoints in the period through +14 days (names masked)',
                ],
            ],
            'completed_rows' => $completedInPeriod
                ->values()
                ->map(fn (Task $task, int $index): array => [
                    'item'   => 'Deliverable #'.($index + 1),
                    'status' => $indonesian ? 'Selesai' : 'Completed',
                    'date'   => $task->updated_at?->toDateString() ?? '—',
                ])
                ->all(),
            'open_rows' => $openTasks
                ->values()
                ->map(function (Task $task, int $index) use ($indonesian, $end): array {
                    $isLate = $task->deadline !== null && $task->deadline->lt($end);

                    return [
                        'item'   => 'Work item #'.($index + 1),
                        'status' => $isLate
                            ? ($indonesian ? 'Perlu perhatian' : 'Needs attention')
                            : ($indonesian ? 'Berjalan' : 'In progress'),
                        'date'   => $task->deadline?->toDateString() ?? '—',
                    ];
                })
                ->all(),
            'checkpoint_rows' => $checkpoints
                ->values()
                ->map(fn (Milestone $milestone, int $index): array => [
                    'item'   => 'Checkpoint #'.($index + 1),
                    'status' => $indonesian ? 'Terjadwal' : 'Scheduled',
                    'date'   => $milestone->deadline?->toDateString() ?? '—',
                ])
                ->all(),
            'customer_notes' => trim($customerNotes),
        ];
    }

    /**
     * @return Collection<int, Milestone>
     */
    protected function upcomingCheckpoints(Project $project, Carbon $start, Carbon $end): Collection
    {
        if (! Schema::hasTable('projects_milestones') || ! $project->allow_milestones) {
            return collect();
        }

        return Milestone::query()
            ->where('project_id', $project->id)
            ->where('is_completed', false)
            ->whereNotNull('deadline')
            ->whereDate('deadline', '>=', $start->toDateString())
            ->whereDate('deadline', '<=', $end->copy()->addDays(14)->toDateString())
            ->orderBy('deadline')
            ->get(['id', 'deadline', 'is_completed']);
    }
}
