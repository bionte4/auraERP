<?php

namespace Webkul\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Employee\Database\Factories\EmploymentTypeFactory;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Country;

class EmploymentType extends Model implements Sortable
{
    use HasCustomFields, HasFactory, SortableTrait;

    protected $table = 'employees_employment_types';

    protected $fillable = [
        'name',
        'country_id',
        'creator_id',
        'code',
        'sort',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employmentType) {
            $employmentType->name = is_string($employmentType->name) ? trim((string) preg_replace('/\s+/', ' ', $employmentType->name)) : $employmentType->name;
            $employmentType->code ??= $employmentType->name;

            $employmentType->creator_id ??= Auth::id();

            static::guardAgainstDuplicateName($employmentType);
        });

        static::updating(function ($employmentType) {
            if ($employmentType->isDirty('name')) {
                $employmentType->name = trim((string) preg_replace('/\s+/', ' ', (string) $employmentType->name));

                static::guardAgainstDuplicateName($employmentType);
            }
        });
    }

    /**
     * Employment types are shared master data: names must stay unique
     * regardless of letter case or extra spaces, otherwise imports and
     * dropdowns end up with near-duplicate variants.
     */
    protected static function guardAgainstDuplicateName(self $employmentType): void
    {
        $normalized = mb_strtolower(trim((string) preg_replace('/\s+/', ' ', (string) $employmentType->name)));

        if ($normalized === '') {
            return;
        }

        $duplicate = static::query()
            ->whereRaw('LOWER(name) = ?', [$normalized])
            ->when($employmentType->exists, fn ($query) => $query->whereKeyNot($employmentType->getKey()))
            ->exists();

        if ($duplicate) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'name' => __('employees::models/employment-type.duplicate-name'),
            ]);
        }
    }

    protected static function newFactory(): EmploymentTypeFactory
    {
        return EmploymentTypeFactory::new();
    }
}
