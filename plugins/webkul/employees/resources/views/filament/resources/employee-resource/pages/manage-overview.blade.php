@php
    $data = $this->getOverviewData();
@endphp

<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                <div class="text-xs text-gray-500">{{ __('employees::filament/resources/employee/pages/manage-overview.cards.department') }}</div>
                <div class="mt-1 font-semibold text-gray-950 dark:text-white">{{ $data['department'] }}</div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                <div class="text-xs text-gray-500">{{ __('employees::filament/resources/employee/pages/manage-overview.cards.related-user') }}</div>
                <div class="mt-1 font-semibold text-gray-950 dark:text-white">{{ $data['user'] }}</div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                <div class="text-xs text-gray-500">{{ __('employees::filament/resources/employee/pages/manage-overview.cards.capacity') }}</div>
                <div class="mt-1 font-semibold text-gray-950 dark:text-white">{{ $data['capacity'] }}</div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                <div class="text-xs text-gray-500">{{ __('employees::filament/resources/employee/pages/manage-overview.cards.contract') }}</div>
                <div class="mt-1 font-semibold text-gray-950 dark:text-white">{{ $data['contract_end'] }}</div>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-4 lg:col-span-2 dark:border-gray-700 dark:bg-gray-900">
                <h3 class="font-semibold text-gray-950 dark:text-white">
                    {{ __('employees::filament/resources/employee/pages/manage-overview.sections.projects') }}
                </h3>
                <ul class="mt-3 space-y-2 text-sm">
                    @forelse ($data['projects'] as $project)
                        <li class="flex justify-between gap-3">
                            <a class="text-primary-600 hover:underline" href="{{ $project['href'] }}">{{ $project['name'] }}</a>
                            <span class="text-gray-500">{{ $project['value'] }}</span>
                        </li>
                    @empty
                        <li class="text-gray-500">—</li>
                    @endforelse
                </ul>
            </div>
            <div class="space-y-4">
                <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                    <div class="text-xs text-gray-500">{{ __('employees::filament/resources/employee/pages/manage-overview.cards.leave') }}</div>
                    <div class="mt-1 text-2xl font-semibold">{{ $data['leave_count'] }}</div>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                    <div class="text-xs text-gray-500">{{ __('employees::filament/resources/employee/pages/manage-overview.cards.assets') }}</div>
                    <div class="mt-1 text-2xl font-semibold">{{ $data['assets'] }}</div>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                    <div class="text-xs text-gray-500">{{ __('employees::filament/resources/employee/pages/manage-overview.cards.evaluation') }}</div>
                    <div class="mt-1 font-semibold">{{ $data['evaluation'] }}</div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
