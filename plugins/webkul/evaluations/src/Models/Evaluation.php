<?php

namespace Webkul\Evaluation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Employee\Models\Employee;
use Webkul\Evaluation\Database\Factories\EvaluationFactory;
use Webkul\Evaluation\Enums\EvaluationStatus;
use Webkul\Support\Traits\BelongsToCompany;

class Evaluation extends Model
{
    use BelongsToCompany;
    use HasFactory;

    protected $table = 'evaluations_evaluations';

    protected $fillable = [
        'company_id',
        'employee_id',
        'reviewer_id',
        'title',
        'period_start',
        'period_end',
        'status',
        'summary',
        'employee_comment',
    ];

    protected static function newFactory(): EvaluationFactory
    {
        return EvaluationFactory::new();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end'   => 'date',
            'status'       => EvaluationStatus::class,
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reviewer_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(EvaluationRating::class)->orderBy('sort')->orderBy('id');
    }

    public function finalScore(): ?float
    {
        $ratings = $this->relationLoaded('ratings') ? $this->ratings : $this->ratings()->get();
        $scored = $ratings->filter(fn (EvaluationRating $rating): bool => $rating->score !== null && $rating->weight > 0);
        $weight = (int) $scored->sum('weight');

        if ($weight <= 0) {
            return null;
        }

        $weighted = $scored->sum(fn (EvaluationRating $rating): float => $rating->score * $rating->weight);

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
        static::saving(function (Evaluation $evaluation): void {
            if (! $evaluation->employee_id) {
                return;
            }

            $companyId = Employee::query()->whereKey($evaluation->employee_id)->value('company_id');

            if ($companyId) {
                $evaluation->company_id = $companyId;
            }
        });
    }
}
