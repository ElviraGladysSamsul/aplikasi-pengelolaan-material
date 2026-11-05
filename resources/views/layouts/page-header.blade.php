<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

    {{-- TITLE --}}
    <div>
        <h1 class="text-xl font-semibold text-gray-800">
            {{ $title }}
        </h1>

        @isset($subtitle)
            <p class="text-sm text-gray-500">
                {{ $subtitle }}
            </p>
        @endisset
    </div>

    {{-- RIGHT AREA (FILTER + ACTION) --}}
    <div class="flex flex-wrap items-center gap-3">
        {{ $slot }}
    </div>

</div>
