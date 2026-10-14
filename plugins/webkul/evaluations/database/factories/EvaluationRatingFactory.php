<?php

namespace Webkul\Evaluation\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Evaluation\Models\Evaluation;
use Webkul\Evaluation\Models\EvaluationRating;

/**
 * @extends Factory<EvaluationRating>
 */
class EvaluationRatingFactory extends Factory
{
    protected $model = EvaluationRating::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'evaluation_id' => Evaluation::factory(),
            'title'         => fake()->sentence(3),
            'weight'        => 25,
            'score'         => null,
            'comment'       => null,
            'sort'          => 0,
        ];
    }
}
