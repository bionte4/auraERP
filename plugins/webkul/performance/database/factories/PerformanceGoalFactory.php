<?php

namespace Webkul\Performance\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Performance\Enums\GoalStatus;
use Webkul\Performance\Models\PerformanceGoal;
use Webkul\Performance\Models\PerformancePlan;

/**
 * @extends Factory<PerformanceGoal>
 */
class PerformanceGoalFactory extends Factory
{
    protected $model = PerformanceGoal::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'performance_plan_id' => PerformancePlan::factory(),
            'title'               => fake()->sentence(4),
            'metric'              => fake()->word(),
            'target'              => (string) fake()->numberBetween(1, 100),
            'weight'              => 25,
            'progress'            => 0,
            'status'              => GoalStatus::NotStarted,
            'notes'               => null,
            'sort'                => 0,
        ];
    }
}
