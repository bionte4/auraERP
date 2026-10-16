@php
    $day = $this->getDayData();
@endphp

<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <h2 class="text-lg font-semibold text-gray-950 dark:text-white">{{ $day['greeting'] }}</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $day['role'] }}</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($day['cards'] as $card)
                <a
                    href="{{ $card['href'] }}"
                    class="block rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-primary-400 dark:border-gray-700 dark:bg-gray-900"
                >
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $card['label'] }}</div>
                    <div class="mt-2 text-2xl font-semibold text-gray-950 dark:text-white">{{ $card['value'] }}</div>
                </a>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
