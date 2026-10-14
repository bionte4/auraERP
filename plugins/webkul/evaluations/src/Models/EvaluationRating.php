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
}
