<div class="bg-white rounded-xl shadow overflow-hidden">

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-gray-700">
            {{ $slot }}
        </table>
    </div>

    @isset($pagination)
        <div class="px-4 py-3 border-t bg-gray-50">
            {{ $pagination }}
        </div>
    @endisset

</div>
