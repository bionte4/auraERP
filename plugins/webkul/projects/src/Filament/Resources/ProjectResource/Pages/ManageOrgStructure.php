<?php

namespace Webkul\Project\Filament\Resources\ProjectResource\Pages;

use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Project\Filament\Resources\ProjectResource;

class ManageOrgStructure extends Page
{
    use InteractsWithRecord;

    protected static string $resource = ProjectResource::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected string $view = 'projects::filament.resources.project-resource.pages.manage-org-structure';

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/resources/project/pages/manage-org-structure.title');
    }

    public function getTitle(): string|Htmlable
    {
        return __('projects::filament/resources/project/pages/manage-org-structure.title');
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }
}
