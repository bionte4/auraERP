<?php

namespace Webkul\Performance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Performance\Database\Factories\PerformanceGoalFactory;
use Webkul\Performance\Enums\GoalStatus;

class PerformanceGoal extends Model
{
    use HasFactory;

    protected $table = 'performance_goals';

    protected $fillable = [
        'performance_plan_id',
        'title',
        'metric',
        'target',
        'weight',
        'progress',
        'status',
        'notes',
        'sort',
    ];

    protected static function newFactory(): PerformanceGoalFactory
    {
        return PerformanceGoalFactory::new();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'weight'   => 'integer',
            'progress' => 'integer',
            'sort'     => 'integer',
            'status'   => GoalStatus::class,
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PerformancePlan::class, 'performance_plan_id');
    }
}
