<?php

namespace Webkul\Performance\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Models\Employee;
use Webkul\Performance\Enums\PlanStatus;
use Webkul\Performance\Models\PerformancePlan;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

/**
 * @extends Factory<PerformancePlan>
 */
class PerformancePlanFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = PerformancePlan::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = now()->startOfQuarter();

        return [
            'employee_id'  => Employee::factory(),
            'title'        => fake()->sentence(3),
            'period_start' => $start->toDateString(),
            'period_end'   => $start->copy()->endOfQuarter()->toDateString(),
            'status'       => PlanStatus::Draft,
            'summary'      => null,
        ];
    }
}
