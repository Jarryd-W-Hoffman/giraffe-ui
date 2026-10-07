<?php

namespace JayAitch\GiraffeUI\Support;

/**
 * Maths for the server-rendered SVG charts: rounded axis ticks, scaling values into a 0–1000 viewBox, SVG paths
 * with gaps for missing values, and number formatting.
 */
class Chart
{
    /**
     * The drawing's viewBox is 0–1000 on both axes; it stretches to the plot, so lines use non-scaling strokes.
     */
    public const SIZE = 1000;

    /**
     * Rounded axis bounds and ticks that cover the values, e.g. 0, 1,000, 2,000 rather than 0, 937, 1,874.
     *
     * @param  array<int, float|int|null>  $values
     * @return array{min: float, max: float, ticks: list<float>}
     */
    public static function ticks(array $values, ?float $floor = null, int $count = 5): array
    {
        $numbers = array_values(array_filter($values, fn ($value) => is_numeric($value)));

        if ($numbers === []) {
            return ['min' => 0.0, 'max' => 1.0, 'ticks' => [0.0, 1.0]];
        }

        $low = min($floor ?? min($numbers), min($numbers));
        $high = max($numbers);

        if ($low == $high) {
            // A flat line still needs a range to sit in.
            $pad = $low == 0 ? 1 : abs($low) * 0.1;
            [$low, $high] = [$low - ($floor === null ? $pad : 0), $high + $pad];
        }

        $step = self::niceStep(($high - $low) / max(1, $count - 1));
        $min = floor($low / $step) * $step;
        $max = ceil($high / $step) * $step;

        $ticks = [];
        for ($tick = $min; $tick <= $max + $step / 2; $tick += $step) {
            $ticks[] = round($tick, 10);
        }

        return ['min' => (float) $min, 'max' => (float) $max, 'ticks' => $ticks];
    }

    /**
     * About one tick per 55px of plot, between three and six, so a short chart isn't crowded with numbers.
     */
    public static function tickCount(int $height): int
    {
        return max(3, min(6, intdiv($height, 55) + 1));
    }

    /**
     * How many decimals a set of ticks needs so they all read alike: 99.90%, 99.92% rather than 99.9%, 99.92%.
     *
     * @param  list<float>  $ticks
     */
    public static function decimals(array $ticks): int
    {
        return (int) collect($ticks)->map(fn ($tick) => strlen(rtrim(substr(strrchr(rtrim(sprintf('%.10F', $tick), '0'), '.') ?: '.', 1), '0')))->max();
    }

    /**
     * The nearest of 1, 2, 2.5 and 5 times a power of ten to the raw step, so the axis hugs the data.
     */
    public static function niceStep(float $raw): float
    {
        if ($raw <= 0) {
            return 1.0;
        }

        $power = 10 ** floor(log10($raw));
        $best = $power;

        // On a tie, the smaller step wins.
        foreach ([2, 2.5, 5, 10] as $multiple) {
            if (abs(log($multiple * $power / $raw)) < abs(log($best / $raw))) {
                $best = $multiple * $power;
            }
        }

        return $best;
    }

    /**
     * A value's distance from the top of the plot, 0–1000 (SVG y grows downwards).
     */
    public static function y(float|int $value, float $min, float $max): float
    {
        return round(self::SIZE - ($value - $min) / ($max - $min) * self::SIZE, 2);
    }

    /**
     * Point i of n, left to right across the plot, 0–1000.
     */
    public static function x(int $index, int $count): float
    {
        return $count < 2 ? self::SIZE / 2 : round($index / ($count - 1) * self::SIZE, 2);
    }

    /**
     * An SVG path through the values. A missing value (null) breaks the line rather than dropping to zero. curve
     * smooths it without overshooting: a curve never bulges past the points either side of it.
     *
     * @param  array<int, float|int|null>  $values
     */
    public static function line(array $values, float $min, float $max, bool $curve = false): string
    {
        return implode('', array_map(fn ($run) => self::path($run, $curve), self::runs($values, $min, $max)));
    }

    /**
     * The line closed down to the baseline, for an area's soft fill. One closed shape per unbroken run.
     *
     * @param  array<int, float|int|null>  $values
     */
    public static function area(array $values, float $min, float $max, bool $curve = false): string
    {
        $base = self::y(max($min, min(0, $max)), $min, $max);

        return implode('', array_map(
            fn ($run) => count($run) < 2 ? '' : self::path($run, $curve).'L'.end($run)[0].','.$base.'L'.$run[0][0].','.$base.'Z',
            self::runs($values, $min, $max),
        ));
    }

    /**
     * The band between two lines, for a stacked area: along the top, then back along the bottom.
     *
     * @param  list<float|int>  $upper
     * @param  list<float|int>  $lower
     */
    public static function band(array $upper, array $lower, float $min, float $max, bool $curve = false): string
    {
        $count = count($upper);
        $top = array_map(fn ($index) => [self::x($index, $count), self::y($upper[$index], $min, $max)], array_keys($upper));
        $bottom = array_reverse(array_map(fn ($index) => [self::x($index, $count), self::y($lower[$index], $min, $max)], array_keys($lower)));

        return $count < 2 ? '' : self::path($top, $curve).'L'.substr(self::path($bottom, $curve), 1).'Z';
    }

    /**
     * Runs of consecutive points as [x, y] in the viewBox, split wherever a value is missing.
     *
     * @param  array<int, float|int|null>  $values
     * @return list<list<array{0: float, 1: float}>>
     */
    protected static function runs(array $values, float $min, float $max): array
    {
        $runs = [[]];
        $count = count($values);

        foreach (array_values($values) as $index => $value) {
            if (! is_numeric($value)) {
                $runs[] = [];

                continue;
            }

            $runs[array_key_last($runs)][] = [self::x($index, $count), self::y($value, $min, $max)];
        }

        return array_values(array_filter($runs));
    }

    /**
     * A path through points: straight segments, or a monotone cubic curve (Fritsch–Carlson), which passes through
     * every point and never overshoots, so a peak in the line is always a real value.
     *
     * @param  list<array{0: float, 1: float}>  $points
     */
    protected static function path(array $points, bool $curve): string
    {
        $at = fn ($point) => round($point[0], 2).','.round($point[1], 2);
        $path = 'M'.$at($points[0]);
        $n = count($points);

        if (! $curve || $n < 3) {
            for ($i = 1; $i < $n; $i++) {
                $path .= 'L'.$at($points[$i]);
            }

            return $path;
        }

        // Slopes between points, then a tangent at each point that is flat at peaks and valleys.
        $slopes = [];
        for ($i = 0; $i < $n - 1; $i++) {
            $slopes[] = ($points[$i + 1][1] - $points[$i][1]) / ($points[$i + 1][0] - $points[$i][0]);
        }
        $tangents = [$slopes[0]];
        for ($i = 1; $i < $n - 1; $i++) {
            $tangents[] = $slopes[$i - 1] * $slopes[$i] <= 0 ? 0 : ($slopes[$i - 1] + $slopes[$i]) / 2;
        }
        $tangents[] = $slopes[$n - 2];
        for ($i = 0; $i < $n - 1; $i++) {
            if ($slopes[$i] == 0) {
                $tangents[$i] = $tangents[$i + 1] = 0;

                continue;
            }
            $a = $tangents[$i] / $slopes[$i];
            $b = $tangents[$i + 1] / $slopes[$i];
            if ($a * $a + $b * $b > 9) {
                $t = 3 / sqrt($a * $a + $b * $b);
                $tangents[$i] = $t * $a * $slopes[$i];
                $tangents[$i + 1] = $t * $b * $slopes[$i];
            }
        }

        for ($i = 0; $i < $n - 1; $i++) {
            $dx = ($points[$i + 1][0] - $points[$i][0]) / 3;
            $path .= 'C'.$at([$points[$i][0] + $dx, $points[$i][1] + $tangents[$i] * $dx])
                .' '.$at([$points[$i + 1][0] - $dx, $points[$i + 1][1] - $tangents[$i + 1] * $dx])
                .' '.$at($points[$i + 1]);
        }

        return $path;
    }

    /**
     * A number for people: thousands separators, or compact (12.5K, 4.2M, 310K), with an optional prefix and suffix.
     */
    public static function format(float|int|null $value, string $prefix = '', string $suffix = '', ?int $decimals = null, bool $compact = false): string
    {
        if (! is_numeric($value)) {
            return '–';
        }

        $sign = $value < 0 ? '-' : '';
        $value = abs($value);

        if ($compact && $value >= 1000) {
            foreach ([[1e12, 'T'], [1e9, 'B'], [1e6, 'M'], [1e3, 'K']] as [$size, $unit]) {
                if ($value >= $size) {
                    $short = $value / $size;

                    return $sign.$prefix.self::trim(number_format($short, $short < 100 ? 1 : 0)).$unit.$suffix;
                }
            }
        }

        $decimals ??= floor($value) == $value ? 0 : min(2, strlen(rtrim(substr(strrchr((string) $value, '.') ?: '', 1), '0')));

        return $sign.$prefix.number_format($value, $decimals).$suffix;
    }

    /**
     * Drop trailing zeros after a decimal point only: 1.50 becomes 1.5 and 2.0 becomes 2, but 10 stays 10.
     */
    protected static function trim(string $number): string
    {
        return str_contains($number, '.') ? rtrim(rtrim($number, '0'), '.') : $number;
    }
}
