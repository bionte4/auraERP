<?php

namespace Webkul\Employee\Data;

class IndonesianCities
{
    /**
     * @var array<int, array{code: string, name: string, province: string, latitude: float, longitude: float}>|null
     */
    protected static ?array $cities = null;

    /**
     * @return array<int, array{code: string, name: string, province: string, latitude: float, longitude: float}>
     */
    public static function all(): array
    {
        if (static::$cities !== null) {
            return static::$cities;
        }

        $path = __DIR__.'/indonesian-cities.json';
        $decoded = json_decode((string) file_get_contents($path), true);

        static::$cities = is_array($decoded) ? $decoded : [];

        return static::$cities;
    }

    /**
     * @return array<string, string>
     */
    public static function options(?string $current = null): array
    {
        $options = [];

        foreach (static::all() as $city) {
            $name = (string) ($city['name'] ?? '');

            if ($name === '') {
                continue;
            }

            $province = (string) ($city['province'] ?? '');
            $options[$name] = $province !== '' ? $name.', '.$province : $name;
        }

        if (filled($current) && ! array_key_exists($current, $options)) {
            $options[$current] = $current;
        }

        return $options;
    }

    /**
     * @return array{code: string, name: string, province: string, latitude: float, longitude: float}|null
     */
    public static function find(string $name): ?array
    {
        foreach (static::all() as $city) {
            if (($city['name'] ?? null) === $name) {
                return $city;
            }
        }

        return null;
    }

    /**
     * @return array{latitude: float, longitude: float}|null
     */
    public static function coordinates(string $name): ?array
    {
        $city = static::find($name);

        if (! $city) {
            return null;
        }

        return [
            'latitude'  => (float) $city['latitude'],
            'longitude' => (float) $city['longitude'],
        ];
    }
}
