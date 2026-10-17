<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->form }}

        @php($preview = $this->preview)

        @if ($preview)
            <div class="mb-2 flex flex-wrap items-center gap-2 text-sm">
                <span class="inline-flex items-center rounded-md bg-primary-50 px-2.5 py-1 font-medium text-primary-700 ring-1 ring-inset ring-primary-600/20 dark:bg-primary-400/10 dark:text-primary-300 dark:ring-primary-400/30">
                    {{ $preview['cover']['customer'] }}
                </span>
                <span class="text-gray-500 dark:text-gray-400">{{ $preview['week_label'] }}</span>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ __('projects::filament/pages/customer-project-weekly-report.preview.cover') }}
                    </p>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                        {{ $preview['cover']['project'] }}
                        · {{ $preview['cover']['manager'] }}
                    </p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">
                                    {{ __('projects::filament/pages/customer-project-weekly-report.table.metric') }}
                                </th>
                                <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-300">
                                    {{ __('projects::filament/pages/customer-project-weekly-report.table.value') }}
                                </th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">
                                    {{ __('projects::filament/pages/customer-project-weekly-report.table.notes') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($preview['metrics'] as $row)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-950 dark:text-white">{{ $row['metric'] }}</td>
                                    <td class="px-4 py-3 text-center tabular-nums text-gray-700 dark:text-gray-200">{{ $row['value'] }}</td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $row['notes'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if (($preview['customer_notes'] ?? '') !== '')
                <div class="rounded-xl border border-gray-200 bg-white px-5 py-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ __('projects::filament/pages/customer-project-weekly-report.preview.notes') }}
                    </p>
                    <p class="mt-2 whitespace-pre-line text-sm text-gray-600 dark:text-gray-300">{{ $preview['customer_notes'] }}</p>
                </div>
            @endif

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ __('projects::filament/pages/customer-project-weekly-report.preview.completed') }}
                    </p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">
                                    {{ __('projects::filament/pages/customer-project-weekly-report.table.item') }}
                                </th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">
                                    {{ __('projects::filament/pages/customer-project-weekly-report.table.status') }}
                                </th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">
                                    {{ __('projects::filament/pages/customer-project-weekly-report.table.date') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse ($preview['completed_rows'] as $row)
                                <tr>
                                    <td class="px-4 py-3 text-gray-950 dark:text-white">{{ $row['item'] }}</td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $row['status'] }}</td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $row['date'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        {{ __('projects::filament/pages/customer-project-weekly-report.preview.empty') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ __('projects::filament/pages/customer-project-weekly-report.preview.open') }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('projects::filament/pages/customer-project-weekly-report.preview.masked-hint') }}
                    </p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">
                                    {{ __('projects::filament/pages/customer-project-weekly-report.table.item') }}
                                </th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">
                                    {{ __('projects::filament/pages/customer-project-weekly-report.table.status') }}
                                </th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">
                                    {{ __('projects::filament/pages/customer-project-weekly-report.table.date') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse ($preview['open_rows'] as $row)
                                <tr>
                                    <td class="px-4 py-3 text-gray-950 dark:text-white">{{ $row['item'] }}</td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $row['status'] }}</td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $row['date'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        {{ __('projects::filament/pages/customer-project-weekly-report.preview.empty') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('projects::filament/pages/customer-project-weekly-report.preview.select-project') }}
            </p>
        @endif
    </div>
</x-filament-panels::page>
