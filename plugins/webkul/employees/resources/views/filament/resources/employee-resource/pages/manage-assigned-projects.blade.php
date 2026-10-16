<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->form }}

        @php
            $assigned = $this->assignedProjects;
        @endphp

        <x-filament::section>
            <x-slot name="heading">
                {{ __('employees::filament/resources/employee/pages/manage-assigned-projects.table.heading') }}
            </x-slot>

            @if ($assigned->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('employees::filament/resources/employee/pages/manage-assigned-projects.table.empty') }}
                </p>
            @else
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-white/10">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10">
                        <thead class="bg-gray-50 dark:bg-white/5">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __('employees::filament/resources/employee/pages/manage-assigned-projects.table.project') }}
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __('employees::filament/resources/employee/pages/manage-assigned-projects.table.customer') }}
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __('employees::filament/resources/employee/pages/manage-assigned-projects.table.manager') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                            @foreach ($assigned as $project)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $project->name }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $project->partner?->name ?? __('employees::filament/resources/employee/pages/manage-assigned-projects.table.none') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $project->user?->name ?? __('employees::filament/resources/employee/pages/manage-assigned-projects.table.none') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
