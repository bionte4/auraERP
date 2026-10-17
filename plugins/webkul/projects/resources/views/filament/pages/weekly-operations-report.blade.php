<x-filament-panels::page>
    <div class="mb-4 flex flex-wrap items-center gap-2 text-sm">
        <span class="inline-flex items-center rounded-md bg-primary-50 px-2.5 py-1 font-medium text-primary-700 ring-1 ring-inset ring-primary-600/20 dark:bg-primary-400/10 dark:text-primary-300 dark:ring-primary-400/30">
            {{ $scopeLabel }}
        </span>
        <span class="text-gray-500 dark:text-gray-400">{{ $weekLabel }}</span>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
            <p class="text-sm font-medium text-gray-900 dark:text-white">
                {{ __('projects::filament/pages/weekly-operations-report.snapshot.title') }}
            </p>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                {{ __('projects::filament/pages/weekly-operations-report.subtitle') }}
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">
                            {{ __('projects::filament/pages/weekly-operations-report.table.metric') }}
                        </th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-300">
                            {{ __('projects::filament/pages/weekly-operations-report.table.value') }}
                        </th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">
                            {{ __('projects::filament/pages/weekly-operations-report.table.notes') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($rows as $row)
                        @php
                            $needsAttention = in_array($row['key'] ?? '', ['pending_leave', 'expiring_contracts', 'overdue_tasks', 'without_user'], true)
                                && (int) $row['value'] > 0;
                        @endphp
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-950 dark:text-white">
                                @if (! empty($row['href']))
                                    <a href="{{ $row['href'] }}" class="text-primary-600 hover:underline dark:text-primary-400">
                                        {{ $row['metric'] }}
                                    </a>
                                @else
                                    {{ $row['metric'] }}
                                @endif
                            </td>
                            <td @class([
                                'px-4 py-3 text-center tabular-nums',
                                'font-semibold text-amber-600 dark:text-amber-400' => $needsAttention,
                                'text-gray-700 dark:text-gray-200' => ! $needsAttention,
                            ])>
                                {{ $row['value'] }}
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $row['notes'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
            <p class="text-sm font-medium text-gray-900 dark:text-white">
                {{ __('projects::filament/pages/weekly-operations-report.load.title') }}
            </p>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                {{ __('projects::filament/pages/weekly-operations-report.load.subtitle') }}
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">
                            {{ __('projects::filament/pages/weekly-operations-report.load.employee') }}
                        </th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-300">
                            {{ __('projects::filament/pages/weekly-operations-report.load.projects') }}
                        </th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-300">
                            {{ __('projects::filament/pages/weekly-operations-report.load.hours') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse (($loadRows ?? []) as $row)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-950 dark:text-white">{{ $row['name'] }}</td>
                            <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-200">{{ $row['projects'] }}</td>
                            <td class="px-4 py-3 text-center tabular-nums text-gray-700 dark:text-gray-200">{{ number_format($row['hours'], 1) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                {{ __('projects::filament/pages/weekly-operations-report.load.empty') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
