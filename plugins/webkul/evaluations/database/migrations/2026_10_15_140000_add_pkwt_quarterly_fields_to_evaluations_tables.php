<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('evaluations_evaluations')) {
            Schema::table('evaluations_evaluations', function (Blueprint $table) {
                if (! Schema::hasColumn('evaluations_evaluations', 'evaluation_type')) {
                    $table->string('evaluation_type')->default('pkwt_q1')->after('title');
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'is_final_period')) {
                    $table->boolean('is_final_period')->default(false)->after('evaluation_type');
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'employee_nik')) {
                    $table->string('employee_nik')->nullable()->after('reviewer_id');
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'join_date')) {
                    $table->date('join_date')->nullable()->after('employee_nik');
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'grade_level')) {
                    $table->string('grade_level')->nullable()->after('join_date');
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'division_name')) {
                    $table->string('division_name')->nullable()->after('grade_level');
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'department_name')) {
                    $table->string('department_name')->nullable()->after('division_name');
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'job_title_snapshot')) {
                    $table->string('job_title_snapshot')->nullable()->after('department_name');
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'work_location_name')) {
                    $table->string('work_location_name')->nullable()->after('job_title_snapshot');
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'performance_plan_id')) {
                    $table->unsignedBigInteger('performance_plan_id')->nullable()->after('period_end');
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'goal_achievement_percentage')) {
                    $table->decimal('goal_achievement_percentage', 5, 2)->nullable()->after('performance_plan_id');
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'competencies_met_count')) {
                    $table->unsignedSmallInteger('competencies_met_count')->nullable()->after('goal_achievement_percentage');
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'competencies_total_count')) {
                    $table->unsignedSmallInteger('competencies_total_count')->nullable()->after('competencies_met_count');
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'strengths')) {
                    $table->text('strengths')->nullable()->after('summary');
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'areas_for_improvement')) {
                    $table->text('areas_for_improvement')->nullable()->after('strengths');
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'development_plan')) {
                    $table->text('development_plan')->nullable()->after('areas_for_improvement');
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'recommendation_decision')) {
                    $table->string('recommendation_decision')->nullable()->after('development_plan');
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'permanent_effective_date')) {
                    $table->date('permanent_effective_date')->nullable()->after('recommendation_decision');
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'extension_months')) {
                    $table->unsignedSmallInteger('extension_months')->nullable()->after('permanent_effective_date');
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'recommendation_reason')) {
                    $table->text('recommendation_reason')->nullable()->after('extension_months');
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'employee_signed_at')) {
                    $table->timestamp('employee_signed_at')->nullable()->after('employee_comment');
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'reviewer_signed_at')) {
                    $table->timestamp('reviewer_signed_at')->nullable()->after('employee_signed_at');
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'gm_approver_id')) {
                    $table->foreignId('gm_approver_id')->nullable()->after('reviewer_signed_at')->constrained('employees_employees')->nullOnDelete();
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'gm_signed_at')) {
                    $table->timestamp('gm_signed_at')->nullable()->after('gm_approver_id');
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'director_approver_id')) {
                    $table->foreignId('director_approver_id')->nullable()->after('gm_signed_at')->constrained('employees_employees')->nullOnDelete();
                }

                if (! Schema::hasColumn('evaluations_evaluations', 'director_signed_at')) {
                    $table->timestamp('director_signed_at')->nullable()->after('director_approver_id');
                }
            });
        }

        if (Schema::hasTable('evaluations_ratings')) {
            Schema::table('evaluations_ratings', function (Blueprint $table) {
                if (! Schema::hasColumn('evaluations_ratings', 'description')) {
                    $table->text('description')->nullable()->after('title');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('evaluations_ratings') && Schema::hasColumn('evaluations_ratings', 'description')) {
            Schema::table('evaluations_ratings', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        if (Schema::hasTable('evaluations_evaluations')) {
            Schema::table('evaluations_evaluations', function (Blueprint $table) {
                if (Schema::hasColumn('evaluations_evaluations', 'gm_approver_id')) {
                    $table->dropConstrainedForeignId('gm_approver_id');
                }

                if (Schema::hasColumn('evaluations_evaluations', 'director_approver_id')) {
                    $table->dropConstrainedForeignId('director_approver_id');
                }

                $columns = array_filter([
                    'evaluation_type',
                    'is_final_period',
                    'employee_nik',
                    'join_date',
                    'grade_level',
                    'division_name',
                    'department_name',
                    'job_title_snapshot',
                    'work_location_name',
                    'performance_plan_id',
                    'goal_achievement_percentage',
                    'competencies_met_count',
                    'competencies_total_count',
                    'strengths',
                    'areas_for_improvement',
                    'development_plan',
                    'recommendation_decision',
                    'permanent_effective_date',
                    'extension_months',
                    'recommendation_reason',
                    'employee_signed_at',
                    'reviewer_signed_at',
                    'gm_signed_at',
                    'director_signed_at',
                ], fn (string $col): bool => Schema::hasColumn('evaluations_evaluations', $col));

                if (! empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
