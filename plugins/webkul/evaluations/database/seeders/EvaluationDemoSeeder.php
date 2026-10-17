<?php

namespace Webkul\Evaluation\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\Employee;
use Webkul\Evaluation\Enums\EvaluationStatus;
use Webkul\Evaluation\Enums\EvaluationType;
use Webkul\Evaluation\Enums\RecommendationDecision;
use Webkul\Evaluation\Models\Evaluation;
use Webkul\Performance\Enums\GoalStatus;
use Webkul\Performance\Enums\PlanStatus;
use Webkul\Performance\Models\PerformanceGoal;
use Webkul\Performance\Models\PerformancePlan;
use Webkul\Support\Models\Company;

/**
 * Seeds one easy-to-understand PKWT Q1 evaluation demo:
 * employee Andi Pratama + reviewer Siti Rahayu + scores + narrative.
 */
class EvaluationDemoSeeder extends Seeder
{
    public const EMPLOYEE_NIK = 'EVAL-DEMO-001';

    public const REVIEWER_NIK = 'EVAL-DEMO-REV';

    public function run(): void
    {
        if (! Schema::hasTable('employees_employees') || ! Schema::hasTable('evaluations_evaluations')) {
            $this->command?->error('Tabel employees/evaluations belum siap. Pastikan plugin terpasang.');

            return;
        }

        $company = Company::query()->first();

        if ($company === null) {
            $this->command?->error('Belum ada company. Jalankan erp:install terlebih dahulu.');

            return;
        }

        $department = $this->ensureDepartment($company->id);
        $reviewer = $this->ensureReviewer($company->id, $department?->id);
        $employee = $this->ensureEmployee($company->id, $department?->id, $reviewer->id);
        $periodStart = now()->startOfQuarter()->toDateString();
        $periodEnd = now()->endOfQuarter()->toDateString();
        $plan = $this->ensurePerformancePlan($company->id, $employee, $periodStart, $periodEnd);
        $evaluation = $this->ensureEvaluation($company->id, $employee, $reviewer, $plan, $periodStart, $periodEnd);

        $this->command?->info('Contoh evaluasi kinerja siap:');
        $this->command?->info("  Karyawan : {$employee->name} (NIK {$employee->identification_id})");
        $this->command?->info("  Penilai  : {$reviewer->name} (NIK {$reviewer->identification_id})");
        $this->command?->info("  Evaluasi : {$evaluation->title}");
        $this->command?->info('  Menu     : Admin → Evaluations (cari NIK EVAL-DEMO-001)');
    }

    protected function ensureDepartment(int $companyId): ?Department
    {
        if (! Schema::hasTable('employees_departments')) {
            return null;
        }

        return Department::withoutGlobalScopes()->firstOrCreate(
            [
                'company_id' => $companyId,
                'name'       => 'Engineering (Demo Evaluasi)',
            ],
            [
                'color' => '#3b6ef6',
            ],
        );
    }

    protected function ensureReviewer(int $companyId, ?int $departmentId): Employee
    {
        return Employee::withoutGlobalScopes()->updateOrCreate(
            ['identification_id' => self::REVIEWER_NIK],
            [
                'company_id'    => $companyId,
                'department_id' => $departmentId,
                'name'          => 'Siti Rahayu',
                'job_title'     => 'Supervisor Engineering',
                'work_email'    => 'siti.rahayu.demo@opsora.my.id',
                'employee_type' => 'employee',
                'is_active'     => true,
                'join_date'     => now()->subYears(4)->toDateString(),
                'time_zone'     => 'Asia/Jakarta',
            ],
        );
    }

    protected function ensureEmployee(int $companyId, ?int $departmentId, int $reviewerId): Employee
    {
        return Employee::withoutGlobalScopes()->updateOrCreate(
            ['identification_id' => self::EMPLOYEE_NIK],
            [
                'company_id'    => $companyId,
                'department_id' => $departmentId,
                'parent_id'     => $reviewerId,
                'name'          => 'Andi Pratama',
                'job_title'     => 'Project Engineer',
                'work_email'    => 'andi.pratama.demo@opsora.my.id',
                'employee_type' => 'employee',
                'is_active'     => true,
                'join_date'     => now()->subMonths(5)->toDateString(),
                'time_zone'     => 'Asia/Jakarta',
            ],
        );
    }

    protected function ensurePerformancePlan(
        int $companyId,
        Employee $employee,
        string $periodStart,
        string $periodEnd,
    ): ?PerformancePlan {
        if (! Schema::hasTable('performance_plans') || ! Schema::hasTable('performance_goals')) {
            return null;
        }

        $plan = PerformancePlan::withoutGlobalScopes()->updateOrCreate(
            [
                'employee_id'  => $employee->id,
                'period_start' => $periodStart,
                'period_end'   => $periodEnd,
            ],
            [
                'company_id' => $companyId,
                'title'      => 'Rencana Kinerja Triwulan — Andi Pratama',
                'status'     => PlanStatus::Active,
                'summary'    => 'Contoh rencana kinerja yang terhubung ke evaluasi Triwulan I PKWT.',
            ],
        );

        $goals = [
            [
                'sort'     => 1,
                'title'    => 'Penyelesaian work order tepat waktu',
                'metric'   => '% WO selesai sesuai SLA',
                'target'   => '95%',
                'weight'   => 60,
                'progress' => 97,
                'status'   => GoalStatus::Achieved,
                'notes'    => 'Target tercapai; 2 WO minor terlambat karena material.',
            ],
            [
                'sort'     => 2,
                'title'    => 'Dokumentasi HSE di site',
                'metric'   => 'Kelengkapan checklist HSE',
                'target'   => '100%',
                'weight'   => 40,
                'progress' => 95,
                'status'   => GoalStatus::InProgress,
                'notes'    => 'Checklist hampir lengkap; 1 item foto sebelum/sesudah masih perlu dilengkapi.',
            ],
        ];

        foreach ($goals as $goal) {
            PerformanceGoal::query()->updateOrCreate(
                [
                    'performance_plan_id' => $plan->id,
                    'sort'                => $goal['sort'],
                ],
                $goal,
            );
        }

        return $plan->fresh('goals');
    }

    protected function ensureEvaluation(
        int $companyId,
        Employee $employee,
        Employee $reviewer,
        ?PerformancePlan $plan,
        string $periodStart,
        string $periodEnd,
    ): Evaluation {
        $goalPercentage = $plan?->weightedScore() ?? 96.2;

        $evaluation = Evaluation::withoutGlobalScopes()->updateOrCreate(
            [
                'employee_id'     => $employee->id,
                'evaluation_type' => EvaluationType::PkwtQ1,
                'period_start'    => $periodStart,
                'period_end'      => $periodEnd,
            ],
            [
                'company_id'                  => $companyId,
                'reviewer_id'                 => $reviewer->id,
                'title'                       => 'Contoh Evaluasi Triwulan I PKWT — Andi Pratama',
                'is_final_period'             => false,
                'performance_plan_id'         => $plan?->id,
                'goal_achievement_percentage' => $goalPercentage,
                'competencies_met_count'      => 9,
                'competencies_total_count'    => 10,
                'status'                      => EvaluationStatus::InReview,
                'summary'                     => 'Contoh data demo: Andi menunjukkan kinerja solid pada Triwulan I. Gunakan baris ini sebagai acuan cara mengisi evaluasi / template import.',
                'strengths'                   => "• Disiplin waktu dan komunikasi dengan atasan baik.\n• Hasil kerja rapi dan sesuai spesifikasi teknis.",
                'areas_for_improvement'       => "• Perlu lebih proaktif mengusulkan perbaikan proses di site.\n• Dokumentasi foto progress masih sering terlambat diunggah.",
                'development_plan'            => 'Coaching 2 minggu sekali dengan Supervisor; ikut briefing HSE mingguan; target dokumentasi foto H+1.',
                'recommendation_decision'     => RecommendationDecision::ExtendPkwt,
                'extension_months'            => 6,
                'recommendation_reason'       => 'Memenuhi kriteria minimum untuk perpanjangan PKWT pada evaluasi triwulan pertama.',
            ],
        );

        $evaluation->seedDefaultPkwtCriteria(replaceExisting: true);

        $demoScores = [
            1  => 4,
            2  => 4,
            3  => 4,
            4  => 4,
            5  => 3,
            6  => 4,
            7  => 3,
            8  => 4,
            9  => 3,
            10 => 4,
        ];

        foreach ($evaluation->ratings()->orderBy('sort')->get() as $rating) {
            $sort = (int) $rating->sort;
            $rating->forceFill([
                'score'   => $demoScores[$sort] ?? 3,
                'comment' => $sort === 1
                    ? 'Target WO tercapai hampir seluruhnya.'
                    : ($sort === 7 ? 'Inisiatif mulai muncul, masih perlu didorong.' : null),
            ])->save();
        }

        return $evaluation->fresh(['ratings', 'employee', 'reviewer']);
    }
}
