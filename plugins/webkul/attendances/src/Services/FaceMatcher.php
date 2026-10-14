<?php

namespace Webkul\Attendance\Services;

class FaceMatcher
{
    public const DIMENSIONS = 128;

    public const THRESHOLD = 0.5;

    /**
     * @return array<int, float>|null
     */
    public function normalize(mixed $descriptor): ?array
    {
        if (! is_array($descriptor) || count($descriptor) !== self::DIMENSIONS) {
            return null;
        }

        $values = [];

        foreach (array_values($descriptor) as $value) {
            if (! is_numeric($value)) {
                return null;
            }

            $values[] = (float) $value;
        }

        return $values;
    }

    /**
     * @param  array<int, float>  $probe
     * @param  array<int, float>  $enrolled
     */
    public function distance(array $probe, array $enrolled): float
    {
        $sum = 0.0;

        foreach ($probe as $index => $value) {
            $delta = $value - (float) $enrolled[$index];
            $sum += $delta * $delta;
        }

        return sqrt($sum);
    }

    /**
     * @param  array<int, float>  $probe
     * @param  array<int, float>  $enrolled
     */
    public function matches(array $probe, array $enrolled): bool
    {
        return $this->distance($probe, $enrolled) <= self::THRESHOLD;
    }
}
