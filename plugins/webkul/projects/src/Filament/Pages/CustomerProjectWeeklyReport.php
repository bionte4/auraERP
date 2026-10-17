<?php

namespace Webkul\Project\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
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
            'project_id'      => null,
            'customer_notes'  => '',
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
                            ->live()
                            ->afterStateUpdated(fn () => $this->refreshPreview()),
                        Textarea::make('customer_notes')
                            ->label(__('projects::filament/pages/customer-project-weekly-report.fields.notes'))
                            ->helperText(__('projects::filament/pages/customer-project-weekly-report.fields.notes-helper'))
                            ->rows(3)
                            ->live(debounce: 500)
                            ->afterStateUpdated(fn () => $this->refreshPreview())
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function updatedDataProjectId(): void
    {
        $this->refreshPreview();
    }

    public function refreshPreview(): void
    {
        $projectId = $this->data['project_id'] ?? null;

        if (blank($projectId)) {
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
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download')
                ->label(__('projects::filament/pages/customer-project-weekly-report.actions.download'))
                ->icon('heroicon-o-arrow-down-tray')
                ->disabled(fn (): bool => blank($this->data['project_id'] ?? null))
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

        $project = Project::query()
            ->with(['partner', 'user'])
            ->findOrFail((int) $projectId);

        $notes = (string) ($this->data['customer_notes'] ?? '');
        $slug = str($project->name)->slug('_')->limit(40, '')->toString();
        $filename = 'customer-project-weekly-'.$slug.'-'.now()->format('Y-m-d').'.xlsx';

        return Excel::download(
            CustomerProjectWeeklyExport::make($project, auth()->user(), $notes),
            $filename,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function getViewData(): array
    {
        return [
            'preview' => $this->preview,
        ];
    }
}
