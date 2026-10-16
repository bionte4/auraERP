<?php

namespace Webkul\Employee\Filament\Resources\EmployeeResource\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;
use Webkul\Employee\Filament\Resources\EmployeeResource;
use Webkul\Employee\Services\EmployeeUserProvisioner;
use Webkul\Project\Services\EmployeeCapacity;
use Webkul\Project\Services\ProjectEmployeeAssigner;

class ManageOverview extends Page
{
    use InteractsWithRecord;

    protected static string $resource = EmployeeResource::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-plus';

    protected string $view = 'employees::filament.resources.employee-resource.pages.manage-overview';

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/resources/employee/pages/manage-overview.navigation.label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('employees::filament/resources/employee/pages/manage-overview.title', [
            'name' => $this->getRecord()->name,
        ]);
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('provisionUser')
                ->label(__('employees::filament/resources/employee/pages/manage-overview.actions.provision'))
                ->icon('heroicon-o-user-plus')
                ->color('warning')
                ->visible(fn (): bool => blank($this->getRecord()->user_id))
                ->requiresConfirmation()
                ->action(function (): void {
                    try {
                        $result = app(EmployeeUserProvisioner::class)->provision($this->getRecord()->fresh());
                        $this->record = $this->getRecord()->fresh();

                        $notification = Notification::make()
                            ->success()
                            ->title(__('employees::filament/resources/employee/pages/manage-overview.notifications.provisioned.title'));

                        if ($result['created'] && $result['password']) {
                            $notification->body(__('employees::filament/resources/employee/pages/manage-overview.notifications.provisioned.body', [
                                'email'    => $result['user']->email,
                                'password' => $result['password'],
                            ]));
                        }

                        $notification->send();
                    } catch (RuntimeException $exception) {
                        Notification::make()->danger()->title($exception->getMessage())->send();
                    }
                }),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getOverviewData(): array
    {
        $employee = $this->getRecord()->loadMissing(['user', 'department', 'projects.partner']);
        $capacity = app(EmployeeCapacity::class)->forEmployees([$employee->id])[$employee->id] ?? [
            'projects' => 0,
            'hours'    => 0.0,
            'label'    => '—',
        ];

        $projects = app(ProjectEmployeeAssigner::class)->assignedProjects($employee)
            ->map(fn ($project): array => [
                'name'  => $project->name,
                'value' => method_exists($project, 'formattedProjectValue') ? $project->formattedProjectValue() : '—',
                'href'  => url('/admin/project/projects/'.$project->id),
            ])
            ->all();

        $leaveDays = 0;
        $assets = 0;
        $evaluation = '—';

        try {
            if (Schema::hasTable('time_off_leaves')) {
                $leaveDays = (int) DB::table('time_off_leaves')
                    ->where('employee_id', $employee->id)
                    ->whereYear('request_date_from', now()->year)
                    ->count();
            }
        } catch (Throwable) {
        }

        try {
            foreach (['maintenances_equipments', 'maintenance_equipments'] as $table) {
                if (Schema::hasTable($table) && Schema::hasColumn($table, 'employee_id')) {
                    $assets = (int) DB::table($table)->where('employee_id', $employee->id)->count();
                    break;
                }
            }
        } catch (Throwable) {
        }

        try {
            if (Schema::hasTable('evaluations')) {
                $evaluation = (string) (DB::table('evaluations')
                    ->where('employee_id', $employee->id)
                    ->orderByDesc('id')
                    ->value('status') ?? '—');
            }
        } catch (Throwable) {
        }

        return [
            'name'         => $employee->name,
            'department'   => $employee->department?->name ?? '—',
            'user'         => $employee->user?->email ?? '—',
            'capacity'     => $capacity['label'],
            'projects'     => $projects,
            'leave_count'  => $leaveDays,
            'assets'       => $assets,
            'evaluation'   => $evaluation,
            'contract_end' => $employee->contract_end_date?->format('Y-m-d') ?? '—',
        ];
    }
}
