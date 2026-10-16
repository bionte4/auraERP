<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->form }}

        @php
            $assigned = $this->assignedEmployees;
        @endphp

        @if (filled($this->data['project_id'] ?? null))
            <x-filament::section>
                <x-slot name="heading">
                    {{ __('projects::filament/pages/assign-project-employees.table.heading') }}
                </x-slot>

                @if ($assigned->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('projects::filament/pages/assign-project-employees.table.empty') }}
                    </p>
                @else
                    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-white/10">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10">
                            <thead class="bg-gray-50 dark:bg-white/5">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        {{ __('projects::filament/pages/assign-project-employees.table.name') }}
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        {{ __('projects::filament/pages/assign-project-employees.table.department') }}
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        {{ __('projects::filament/pages/assign-project-employees.table.related-user') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                                @foreach ($assigned as $employee)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $employee->name }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                            {{ $employee->department?->name ?? __('projects::filament/pages/assign-project-employees.table.none') }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                            {{ $employee->user?->name ?? __('projects::filament/pages/assign-project-employees.table.none') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
