<?php

namespace Webkul\PluginManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use ReflectionClass;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class Plugin extends Model implements Sortable
{
    use SortableTrait;

    protected $fillable = [
        'name',
        'author',
        'summary',
        'description',
        'latest_version',
        'license',
        'is_active',
        'is_installed',
        'sort',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function dependencies(): BelongsToMany
    {
        return $this->belongsToMany(
            Plugin::class,
            'plugin_dependencies',
            'plugin_id',
            'dependency_id'
        );
    }

    public function dependents(): BelongsToMany
    {
        return $this->belongsToMany(
            Plugin::class,
            'plugin_dependencies',
            'dependency_id',
            'plugin_id'
        );
    }

    /**
     * Resolve Package metadata from loaded PackageServiceProviders.
     *
     * Prefer provider discovery over Filament plugin class-name mangling
     * (e.g. IntegrationPlugin → IntegrationServiceProvider), which breaks when
     * names are plural/singular mismatched.
     *
     * @return array<string, Package>
     */
    public static function getAllPluginPackages(bool $includeCore = false): array
    {
        $packages = [];

        foreach (array_keys(app()->getLoadedProviders()) as $providerClass) {
            if (! is_subclass_of($providerClass, PackageServiceProvider::class)) {
                continue;
            }

            if (! property_exists($providerClass, 'name') || ! is_string($providerClass::$name) || $providerClass::$name === '') {
                continue;
            }

            $provider = app()->getProvider($providerClass);

            if (! $provider instanceof PackageServiceProvider) {
                continue;
            }

            $package = (function (): mixed {
                return $this->package ?? null;
            })->call($provider);

            if (! $package instanceof Package) {
                $package = new Package;
                $provider->configureCustomPackage($package);
                $package->setBasePath(dirname((new ReflectionClass($providerClass))->getFileName(), 2));
            }

            if ($package->isCore && ! $includeCore) {
                continue;
            }

            $packages[$providerClass::$name] = $package;
        }

        return $packages;
    }

    public function getPackageAttribute(): ?Package
    {
        $packages = static::getAllPluginPackages(includeCore: true);

        return $packages[$this->name] ?? null;
    }

    public function isCorePackage(): bool
    {
        return (bool) ($this->package?->isCore);
    }

    public function getDependenciesFromConfig(): array
    {
        return $this->package?->dependencies ?? [];
    }

    public function getDependentsFromConfig(): array
    {
        $packages = static::getAllPluginPackages(includeCore: true);

        $dependents = [];

        foreach ($packages as $pluginName => $package) {
            if ($pluginName === $this->name) {
                continue;
            }

            if (! in_array($this->name, $package->dependencies, true)) {
                continue;
            }

            $dependents[] = $pluginName;
        }

        return $dependents;
    }
}
