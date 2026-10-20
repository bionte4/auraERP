<?php

namespace Webkul\Security\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Webkul\Security\Filament\Resources\RoleResource;
use Webkul\Security\Models\Role;
use Webkul\Security\Support\AdminAccess;
use Webkul\Support\Enums\NavigationGroup;

/**
 * Read-only overview of what every role can actually do: role cards with
 * totals, a granted/total matrix per plugin group, and expandable
 * permission details. Editing still happens in the Roles resource; this
 * page exists so reviewing access does not require scrolling through
 * ~1.300 checkboxes on the edit form.
 */
class RoleSummary extends Page
{
    protected string $view = 'security::filament.pages.role-summary';

    protected static ?string $slug = 'role-summary';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    public ?int $selectedRoleId = null;

    public string $search = '';

    public function mount(): void
    {
        AdminAccess::ensure();
    }

    public static function canAccess(): bool
    {
        return AdminAccess::check();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return AdminAccess::check();
    }

    public static function getNavigationLabel(): string
    {
        return __('security::filament/pages/role-summary.navigation.label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('security::filament/pages/role-summary.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Setting;
    }

    public function selectRole(?int $roleId): void
    {
        $this->selectedRoleId = $this->selectedRoleId === $roleId ? null : $roleId;
    }

    /**
     * @return Collection<int, Role>
     */
    protected function roles(): Collection
    {
        return once(fn (): Collection => Role::query()
            ->with('permissions')
            ->orderBy('guard_name')
            ->orderBy('name')
            ->get());
    }

    /**
     * Permission names per category, used for the card breakdowns.
     *
     * @return array{resources: array<int, string>, pages: array<int, string>, widgets: array<int, string>}
     */
    protected function categoryPermissionNames(): array
    {
        return once(fn (): array => [
            'resources' => collect(RoleResource::getResources() ?? [])
                ->flatMap(fn (array $entity): array => array_keys(RoleResource::getResourcePermissionOptions($entity)))
                ->unique()
                ->values()
                ->all(),
            'pages'     => array_keys(RoleResource::getPageOptions()),
            'widgets'   => array_keys(RoleResource::getWidgetOptions()),
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function roleCards(): array
    {
        $categories = $this->categoryPermissionNames();
        $poolTotal = RoleResource::getAllFormPermissions()->count();

        return $this->roles()
            ->map(function (Role $role) use ($categories, $poolTotal): array {
                $names = $role->permissions->pluck('name')->all();
                $nameSet = array_flip($names);

                $countIn = fn (array $pool): int => count(array_filter($pool, fn (string $name): bool => isset($nameSet[$name])));

                return [
                    'id'        => $role->id,
                    'name'      => $role->name,
                    'guard'     => $role->guard_name,
                    'total'     => count($names),
                    'poolTotal' => $poolTotal,
                    'users'     => RoleResource::assignedUserCount($role),
                    'resources' => $countIn($categories['resources']),
                    'pages'     => $countIn($categories['pages']),
                    'widgets'   => $countIn($categories['widgets']),
                ];
            })
            ->all();
    }

    /**
     * Matrix rows grouped by category and plugin group. Each row knows,
     * per visible role, how many of its permissions are granted and which.
     *
     * @return array<string, array<string, array<int, array<string, mixed>>>>
     */
    public function matrix(): array
    {
        $roles = $this->visibleRoles();

        $rolePermissionSets = $roles->mapWithKeys(
            fn (Role $role): array => [$role->id => array_flip($role->permissions->pluck('name')->all())]
        );

        $search = trim(mb_strtolower($this->search));

        $buildRows = function (array $items) use ($rolePermissionSets, $search): array {
            $rows = [];

            foreach ($items as $item) {
                $names = $item['permissions'];

                if ($search !== '') {
                    $names = array_values(array_filter(
                        $names,
                        fn (string $name): bool => str_contains(mb_strtolower($name), $search)
                            || str_contains(mb_strtolower($item['label']), $search),
                    ));

                    if ($names === []) {
                        continue;
                    }
                }

                $grants = [];

                foreach ($rolePermissionSets as $roleId => $set) {
                    $granted = array_values(array_filter($names, fn (string $name): bool => isset($set[$name])));

                    $grants[$roleId] = [
                        'count' => count($granted),
                        'names' => $granted,
                    ];
                }

                $rows[] = [
                    'label'  => $item['label'],
                    'total'  => count($item['permissions']),
                    'grants' => $grants,
                ];
            }

            return $rows;
        };

        $matrix = [];

        $resourceGroups = [];

        foreach (RoleResource::getPluginResources() ?? [] as $plugin => $entities) {
            $items = collect($entities)
                ->map(fn (array $entity): array => [
                    'label'       => $entity['model'],
                    'permissions' => array_keys(RoleResource::getResourcePermissionOptions($entity)),
                ])
                ->filter(fn (array $item): bool => $item['permissions'] !== [])
                ->all();

            $rows = $buildRows($items);

            if ($rows !== []) {
                $resourceGroups[$plugin] = $rows;
            }
        }

        if ($resourceGroups !== []) {
            $matrix['resources'] = $resourceGroups;
        }

        foreach (['pages' => RoleResource::getPluginPages(), 'widgets' => RoleResource::getPluginWidgets()] as $category => $groups) {
            $categoryGroups = [];

            foreach ($groups as $plugin => $entities) {
                $items = collect($entities)
                    ->map(fn (array $entity): array => [
                        'label'       => static::permissionLabel($entity),
                        'permissions' => static::permissionNames($entity['permissions'] ?? []),
                    ])
                    ->filter(fn (array $item): bool => $item['permissions'] !== [])
                    ->all();

                $rows = $buildRows($items);

                if ($rows !== []) {
                    $categoryGroups[$plugin] = $rows;
                }
            }

            if ($categoryGroups !== []) {
                $matrix[$category] = $categoryGroups;
            }
        }

        return $matrix;
    }

    /**
     * @return Collection<int, Role>
     */
    public function visibleRoles(): Collection
    {
        if ($this->selectedRoleId === null) {
            return $this->roles();
        }

        return $this->roles()->filter(fn (Role $role): bool => $role->id === $this->selectedRoleId)->values();
    }

    /**
     * Extract permission names from the mixed structures Shield returns
     * (name => label maps, or lists of ['key' => ..., 'label' => ...]).
     *
     * @param  array<mixed>  $permissions
     * @return array<int, string>
     */
    protected static function permissionNames(array $permissions): array
    {
        $names = [];

        foreach ($permissions as $key => $value) {
            if (is_array($value) && isset($value['key'])) {
                $names[] = (string) $value['key'];
            } elseif (is_string($key)) {
                $names[] = $key;
            } elseif (is_string($value)) {
                $names[] = $value;
            }
        }

        return array_values(array_unique($names));
    }

    /**
     * @param  array<string, mixed>  $entity
     */
    protected static function permissionLabel(array $entity): string
    {
        $fqcn = $entity['pageFqcn'] ?? $entity['widgetFqcn'] ?? null;

        if (is_string($fqcn) && $fqcn !== '') {
            return \Illuminate\Support\Str::headline(class_basename($fqcn));
        }

        $names = static::permissionNames($entity['permissions'] ?? []);

        return $names[0] ?? '—';
    }
}
