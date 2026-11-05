@props(['cols' => 2])
<div class="grid grid-cols-1 md:grid-cols-{{ $cols }} gap-4 mb-4">
    {{ $slot }}
</div>
