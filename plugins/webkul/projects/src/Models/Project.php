<?php

namespace Webkul\Project\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Throwable;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Employee\Models\Employee;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Database\Factories\ProjectFactory;
use Webkul\Project\Services\ProjectExpiryNotifier;
use Webkul\Security\Models\User;
use Webkul\Security\Support\OwnerSource;
use Webkul\Security\Traits\HasOwnershipScope;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class Project extends Model implements Sortable
{
    use BelongsToCompany;
    use HasChatter, HasCustomFields, HasFactory, HasLogActivity, HasOwnershipScope, SoftDeletes, SortableTrait;

    public const ACTIVITY_PLAN_PLUGIN = 'projects';

    public const REMINDER_WINDOW_DAYS = 90;

    protected $table = 'projects_projects';

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function getModelTitle(): string
    {
        return __('projects::models/project.title');
    }

    protected $fillable = [
        'name',
        'tasks_label',
        'description',
        'visibility',
        'color',
        'sort',
        'start_date',
        'end_date',
        'project_value',
        'allocated_hours',
        'allow_timesheets',
        'allow_milestones',
        'allow_task_dependencies',
        'is_active',
        'stage_id',
        'partner_id',
        'company_id',
        'user_id',
        'creator_id',
    ];

    protected $casts = [
        'start_date'              => 'date',
        'end_date'                => 'date',
        'project_value'           => 'float',
        'allocated_hours'         => 'integer',
        'is_active'               => 'boolean',
        'allow_timesheets'        => 'boolean',
        'allow_milestones'        => 'boolean',
        'allow_task_dependencies' => 'boolean',
    ];

    protected function getLogAttributeLabels(): array
    {
        return [
            'name'                    => __('projects::models/project.log-attributes.name'),
            'tasks_label'             => __('projects::models/project.log-attributes.tasks_label'),
            'description'             => __('projects::models/project.log-attributes.description'),
            'visibility'              => __('projects::models/project.log-attributes.visibility'),
            'color'                   => __('projects::models/project.log-attributes.color'),
            'sort'                    => __('projects::models/project.log-attributes.sort'),
            'start_date'              => __('projects::models/project.log-attributes.start_date'),
            'end_date'                => __('projects::models/project.log-attributes.end_date'),
            'project_value'           => __('projects::models/project.log-attributes.project_value'),
            'allocated_hours'         => __('projects::models/project.log-attributes.allocated_hours'),
            'allow_timesheets'        => __('projects::models/project.log-attributes.allow_timesheets'),
            'allow_milestones'        => __('projects::models/project.log-attributes.allow_milestones'),
            'allow_task_dependencies' => __('projects::models/project.log-attributes.allow_task_dependencies'),
            'is_active'               => __('projects::models/project.log-attributes.is_active'),
            'stage.name'              => __('projects::models/project.log-attributes.stage'),
            'partner.name'            => __('projects::models/project.log-attributes.partner'),
            'company.name'            => __('projects::models/project.log-attributes.company'),
            'user.name'               => __('projects::models/project.log-attributes.user'),
            'creator.name'            => __('projects::models/project.log-attributes.creator'),
        ];
    }

    public static function autoAssignsCompany(): bool
    {
        return false;
    }

    protected function plannedDate(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['start_date'].' - '.$attributes['end_date'],
        );
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(ProjectStage::class);
    }

    public function taskStages(): HasMany
    {
        return $this->hasMany(TaskStage::class);
    }

    public function favoriteUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'projects_user_project_favorites', 'project_id', 'user_id');
    }

    public function getIsFavoriteByUserAttribute(): bool
    {
        if ($this->relationLoaded('favoriteUsers')) {
            return $this->favoriteUsers->contains('id', Auth::id());
        }

        return $this->favoriteUsers()->where('user_id', Auth::id())->exists();
    }

    public function getRemainingHoursAttribute(): float
    {
        return $this->allocated_hours - $this->tasks->sum('remaining_hours');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'projects_project_tag', 'project_id', 'tag_id');
    }

    public function ownershipSources(): array
    {
        return [
            OwnerSource::column('creator_id'),
            OwnerSource::column('user_id'),
            OwnerSource::followers(),
        ];
    }

    public function daysUntilEndDate(): ?int
    {
        if (! $this->end_date) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->end_date->copy()->startOfDay(), false);
    }

    public function expiryReminderStage(): ?string
    {
        if (! $this->end_date) {
            return null;
        }

        $today = now()->startOfDay();
        $endDate = $this->end_date->copy()->startOfDay();
        $days = (int) $today->diffInDays($endDate, false);

        if ($days < 0) {
            return 'expired';
        }

        if ($days <= 30 || $endDate->lte($today->copy()->addMonth())) {
            return '1_month';
        }

        if ($days <= 60 || $endDate->lte($today->copy()->addMonths(2))) {
            return '2_months';
        }

        if ($days <= self::REMINDER_WINDOW_DAYS || $endDate->lte($today->copy()->addMonths(3))) {
            return '3_months';
        }

        return null;
    }

    public function isEndingSoon(int $withinDays = self::REMINDER_WINDOW_DAYS): bool
    {
        $days = $this->daysUntilEndDate();

        if ($days === null || $days < 0) {
            return false;
        }

        if ($withinDays >= self::REMINDER_WINDOW_DAYS) {
            return in_array($this->expiryReminderStage(), ['1_month', '2_months', '3_months'], true);
        }

        return $days <= $withinDays;
    }

    public function formattedProjectValue(): string
    {
        $value = (float) ($this->project_value ?? 0);

        return 'Rp '.number_format($value, 0, ',', '.');
    }

    public static function filterExternalCustomersQuery(Builder $query, mixed $selectedId = null): Builder
    {
        $hasRankColumn = Schema::hasColumn('partners_partners', 'customer_rank');

        return $query->where(function (Builder $outer) use ($hasRankColumn, $selectedId): void {
            if ($selectedId) {
                $outer->orWhere('partners_partners.id', $selectedId);
            }

            if ($hasRankColumn) {
                $outer->orWhere('partners_partners.customer_rank', '>', 0);
            }

            $outer->orWhere(function (Builder $clean): void {
                $clean->where(function (Builder $sub): void {
                    $sub->whereNull('partners_partners.sub_type')
                        ->orWhereNotIn('partners_partners.sub_type', ['employee', 'invoice', 'delivery', 'other']);
                });

                if (Schema::hasTable('users') && Schema::hasColumn('users', 'partner_id')) {
                    $clean->whereNotIn(
                        'partners_partners.id',
                        User::query()->whereNotNull('partner_id')->select('partner_id'),
                    );
                }

                if (Schema::hasTable('employees_employees') && Schema::hasColumn('employees_employees', 'partner_id')) {
                    $clean->whereNotIn(
                        'partners_partners.id',
                        Employee::withoutGlobalScopes()->whereNotNull('partner_id')->select('partner_id'),
                    );
                }

                if (Schema::hasTable('companies') && Schema::hasColumn('companies', 'partner_id')) {
                    $clean->whereNotIn(
                        'partners_partners.id',
                        Company::query()->whereNotNull('partner_id')->select('partner_id'),
                    );
                }
            });
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function createCustomerPartner(array $data): int
    {
        $partner = Partner::withoutGlobalScopes()->create([
            'name'         => $data['name'],
            'email'        => $data['email'] ?? null,
            'phone'        => $data['phone'] ?? null,
            'account_type' => AccountType::COMPANY,
            'sub_type'     => 'company',
            'company_id'   => current_company_id(),
            'creator_id'   => Auth::id(),
        ]);

        if (Schema::hasColumn('partners_partners', 'customer_rank')) {
            $partner->forceFill(['customer_rank' => 1])->save();
        }

        return (int) $partner->id;
    }

    protected static function newFactory(): ProjectFactory
    {
        return ProjectFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            $project->creator_id ??= Auth::id();
        });

        static::saved(function (self $project): void {
            if (! $project->end_date || ! $project->is_active || $project->expiryReminderStage() === null) {
                return;
            }

            try {
                if (Schema::hasTable('notifications')) {
                    app(ProjectExpiryNotifier::class)->notifyForProject($project);
                }
            } catch (Throwable) {
            }
        });
    }
}
