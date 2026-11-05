<div class="mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

        {{-- LEFT --}}
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">
                {{ $title }}
            </h1>

            @isset($subtitle)
                <p class="text-sm text-gray-500">
                    {{ $subtitle }}
                </p>
            @endisset
        </div>

        {{-- RIGHT (ACTION / FILTER / SEARCH) --}}
        <div class="flex flex-wrap items-center gap-2">
            {{ $slot }}
        </div>

    </div>
</div>
