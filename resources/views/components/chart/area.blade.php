@props([
    'labels' => [],
    'series' => null,
    'data' => null,
    'name' => null,
    'colors' => [],
    'stacked' => true,
    'curve' => false,
    'markers' => false,
    'highlight' => null,
    'reference' => null,
    'referenceLabel' => null,
    'height' => 240,
    'prefix' => '',
    'suffix' => '',
    'compact' => false,
    'decimals' => null,
    'label' => null,
])

{{--
    An area chart: a line chart with its fill on. With several series they stack, so the top edge is the total and each
    band is that series' share; stacked="false" overlaps them instead (best kept to two).
--}}
<x-gui::chart.line
    area
    :labels="$labels"
    :series="$series"
    :data="$data"
    :name="$name"
    :colors="$colors"
    :stacked="$stacked"
    :curve="$curve"
    :markers="$markers"
    :highlight="$highlight"
    :reference="$reference"
    :reference-label="$referenceLabel"
    :height="$height"
    :prefix="$prefix"
    :suffix="$suffix"
    :compact="$compact"
    :decimals="$decimals"
    :label="$label"
    {{ $attributes }}
/>
