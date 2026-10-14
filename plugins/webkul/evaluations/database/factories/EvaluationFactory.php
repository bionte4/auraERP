<?php

namespace Webkul\Evaluation\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Models\Employee;
use Webkul\Evaluation\Enums\EvaluationStatus;
use Webkul\Evaluation\Models\Evaluation;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

/**
 * @extends Factory<Evaluation>
 */
class EvaluationFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = Evaluation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = now()->startOfQuarter();

        return [
            'employee_id'      => Employee::factory(),
            'reviewer_id'      => null,
            'title'            => fake()->sentence(3),
            'period_start'     => $start->toDateString(),
            'period_end'       => $start->copy()->endOfQuarter()->toDateString(),
            'status'           => EvaluationStatus::Draft,
            'summary'          => null,
            'employee_comment' => null,
        ];
    }
}
