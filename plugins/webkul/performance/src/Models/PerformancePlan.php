<?php

namespace Webkul\Performance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Employee\Models\Employee;
use Webkul\Performance\Database\Factories\PerformancePlanFactory;
use Webkul\Performance\Enums\PlanStatus;
use Webkul\Support\Traits\BelongsToCompany;

class PerformancePlan extends Model
{
    use BelongsToCompany;
    use HasFactory;

    protected $table = 'performance_plans';

    protected $fillable = [
        'company_id',
        'employee_id',
        'title',
        'period_start',
        'period_end',
        'status',
        'summary',
    ];

    protected static function newFactory(): PerformancePlanFactory
    {
        return PerformancePlanFactory::new();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end'   => 'date',
            'status'       => PlanStatus::class,
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function goals(): HasMany
    {
        return $this->hasMany(PerformanceGoal::class)->orderBy('sort')->orderBy('id');
    }

    public function weightedScore(): ?float
    {
        $goals = $this->relationLoaded('goals') ? $this->goals : $this->goals()->get();
        $weight = (int) $goals->sum('weight');

        if ($weight <= 0) {
            return null;
        }

        $weighted = $goals->sum(fn (PerformanceGoal $goal): float => $goal->progress * $goal->weight);

        return round($weighted / $weight, 1);
    }

    public function periodLabel(): string
    {
        $start = $this->period_start?->format('d M Y') ?? '—';
        $end = $this->period_end?->format('d M Y') ?? '—';

        return $start.' – '.$end;
    }

    protected static function booted(): void
    {
        static::saving(function (PerformancePlan $plan): void {
            if (! $plan->employee_id) {
                return;
            }

            $companyId = Employee::query()->whereKey($plan->employee_id)->value('company_id');

            if ($companyId) {
                $plan->company_id = $companyId;
            }
        });
    }
}
