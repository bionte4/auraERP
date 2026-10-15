<?php

namespace Webkul\Evaluation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Evaluation\Database\Factories\EvaluationRatingFactory;

class EvaluationRating extends Model
{
    use HasFactory;

    protected $table = 'evaluations_ratings';

    protected $fillable = [
        'evaluation_id',
        'title',
        'description',
        'weight',
        'score',
        'comment',
        'sort',
    ];

    protected static function newFactory(): EvaluationRatingFactory
    {
        return EvaluationRatingFactory::new();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'weight' => 'integer',
            'score'  => 'integer',
            'sort'   => 'integer',
        ];
    }

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public static function scoreScaleLabel(?int $score): string
    {
        return match ($score) {
            5       => '5 - Secara konsisten melampaui standar yang diharapkan',
            4       => '4 - Secara signifikan melebihi standar yang diharapkan',
            3       => '3 - Memenuhi standar yang diharapkan',
            2       => '2 - Kurang dari standar yang diharapkan',
            1       => '1 - Sama sekali tidak memenuhi standar yang diharapkan',
            default => '—',
        };
    }
}
