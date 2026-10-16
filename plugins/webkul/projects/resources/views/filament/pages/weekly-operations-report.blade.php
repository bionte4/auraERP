<x-filament-panels::page>
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
            <p class="text-sm text-gray-600 dark:text-gray-300">
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
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">
                            {{ __('projects::filament/pages/weekly-operations-report.table.value') }}
                        </th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">
                            {{ __('projects::filament/pages/weekly-operations-report.table.notes') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($rows as $row)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-950 dark:text-white">{{ $row['metric'] }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $row['value'] }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $row['notes'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
