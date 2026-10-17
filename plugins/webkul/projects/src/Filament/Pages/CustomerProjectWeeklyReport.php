<?php

namespace Webkul\Project\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Webkul\Project\Exports\CustomerProjectWeeklyExport;
use Webkul\Project\Models\Project;
use Webkul\Project\Services\CustomerProjectWeeklyReportData;
use Webkul\Support\Enums\NavigationGroup;

class CustomerProjectWeeklyReport extends Page implements HasForms
{
    use HasPageShield;
    use InteractsWithForms;

    protected string $view = 'projects::filament.pages.customer-project-weekly-report';

    protected static ?string $slug = 'customer-project-weekly-report';

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    public ?array $data = [];

    /**
     * @var array<string, mixed>|null
     */
    public ?array $preview = null;

    protected static function getPagePermission(): ?string
    {
        return 'page_project_customer_project_weekly_report';
    }

    public static function canAccess(): bool
    {
        if (! parent::canAccess()) {
            return false;
        }

        return app(CustomerProjectWeeklyReportData::class)->viewerMayAccess();
    }

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/pages/customer-project-weekly-report.navigation.label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('projects::filament/pages/customer-project-weekly-report.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Dashboard;
    }

    public function mount(): void
    {
        $this->form->fill([
            'project_id'     => null,
            'start_date'     => now()->startOfWeek()->toDateString(),
            'end_date'       => now()->endOfWeek()->toDateString(),
            'customer_notes' => '',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('projects::filament/pages/customer-project-weekly-report.form.section'))
                    ->description(__('projects::filament/pages/customer-project-weekly-report.subtitle'))
                    ->schema([
                        Select::make('project_id')
                            ->label(__('projects::filament/pages/customer-project-weekly-report.fields.project'))
                            ->options(fn (): array => app(CustomerProjectWeeklyReportData::class)->projectOptions())
                            ->native()
                            ->required()
                            ->live(),
                        DatePicker::make('start_date')
                            ->label(__('projects::filament/pages/customer-project-weekly-report.fields.start_date'))
                            ->native()
                            ->required()
                            ->live(),
                        DatePicker::make('end_date')
                            ->label(__('projects::filament/pages/customer-project-weekly-report.fields.end_date'))
                            ->native()
                            ->required()
                            ->live()
                            ->afterOrEqual('start_date'),
                        Textarea::make('customer_notes')
                            ->label(__('projects::filament/pages/customer-project-weekly-report.fields.notes'))
                            ->helperText(__('projects::filament/pages/customer-project-weekly-report.fields.notes-helper'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns([
                        'default' => 1,
                        'md'      => 3,
                    ]),
            ])
            ->statePath('data');
    }

    public function updatedDataProjectId(): void
    {
        $this->preview = null;
    }

    public function updatedDataStartDate(): void
    {
        $this->preview = null;
    }

    public function updatedDataEndDate(): void
    {
        $this->preview = null;
    }

    public function updatedDataCustomerNotes(): void
    {
        if ($this->preview !== null) {
            $this->refreshPreview();
        }
    }

    public function refreshPreview(): void
    {
        $projectId = $this->data['project_id'] ?? null;

        if (blank($projectId)) {
            $this->preview = null;

            return;
        }

        if (blank($this->data['start_date'] ?? null) || blank($this->data['end_date'] ?? null)) {
            Notification::make()
                ->warning()
                ->title(__('projects::filament/pages/customer-project-weekly-report.notifications.missing-dates'))
                ->send();

            $this->preview = null;

            return;
        }

        $project = Project::query()
            ->with(['partner', 'user'])
            ->find((int) $projectId);

        if (! $project) {
            $this->preview = null;

            return;
        }

        $service = app(CustomerProjectWeeklyReportData::class);

        if (! $service->canViewProject($project)) {
            $this->preview = null;

            Notification::make()
                ->danger()
                ->title(__('projects::filament/pages/customer-project-weekly-report.notifications.forbidden'))
                ->send();

            return;
        }

        $this->preview = $service->build(
            $project,
            auth()->user(),
            (string) ($this->data['customer_notes'] ?? ''),
            $this->data['start_date'] ?? null,
            $this->data['end_date'] ?? null,
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label(__('projects::filament/pages/customer-project-weekly-report.actions.preview'))
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->disabled(fn (): bool => blank($this->data['project_id'] ?? null)
                    || blank($this->data['start_date'] ?? null)
                    || blank($this->data['end_date'] ?? null))
                ->action(function (): void {
                    $this->refreshPreview();

                    if ($this->preview !== null) {
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/pages/customer-project-weekly-report.notifications.preview-ready'))
                            ->send();
                    }
                }),
            Action::make('download')
                ->label(__('projects::filament/pages/customer-project-weekly-report.actions.download'))
                ->icon('heroicon-o-arrow-down-tray')
                ->disabled(fn (): bool => $this->preview === null)
                ->action(fn () => $this->downloadReport()),
        ];
    }

    public function downloadReport(): ?BinaryFileResponse
    {
        $projectId = $this->data['project_id'] ?? null;

        if (blank($projectId)) {
            Notification::make()
                ->danger()
                ->title(__('projects::filament/pages/customer-project-weekly-report.notifications.missing-project'))
                ->send();

            return null;
        }

        if ($this->preview === null) {
            $this->refreshPreview();
        }

        if ($this->preview === null) {
            Notification::make()
                ->warning()
                ->title(__('projects::filament/pages/customer-project-weekly-report.notifications.preview-required'))
                ->send();

            return null;
        }

        $project = Project::query()
            ->with(['partner', 'user'])
            ->findOrFail((int) $projectId);

        $notes = (string) ($this->data['customer_notes'] ?? '');
        $slug = str($project->name)->slug('_')->limit(40, '')->toString();
        $start = (string) ($this->data['start_date'] ?? now()->toDateString());
        $end = (string) ($this->data['end_date'] ?? now()->toDateString());
        $filename = 'customer-project-'.$slug.'-'.$start.'_'.$end.'.xlsx';

        return Excel::download(
            CustomerProjectWeeklyExport::make(
                $project,
                auth()->user(),
                $notes,
                $this->data['start_date'] ?? null,
                $this->data['end_date'] ?? null,
            ),
            $filename,
        );
    }
}
