@php
    $routeName = request()->route()?->getName();
    $breadcrumbs = config('breadcrumbs.' . $routeName);
@endphp

@if (!empty($breadcrumbs))
    <nav class="flex flex-wrap items-center gap-1 text-sm text-gray-500">
        @foreach ($breadcrumbs as $i => $crumb)
            @if (isset($crumb['route']))
                <a href="{{ route($crumb['route']) }}" class="hover:underline text-gray-600">
                    {{ $crumb['label'] }}
                </a>
            @else
                <span class="font-medium text-gray-800">
                    {{ $crumb['label'] }}
                </span>
            @endif

            @if ($i < count($breadcrumbs) - 1)
                <span>/</span>
            @endif
        @endforeach
    </nav>
@endif
