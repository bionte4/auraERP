<x-filament-panels::page>
    @php
        $managers = $this->getManagerSummaries();
        $rows = $this->getProjectRows();
        $unassigned = $this->getUnassignedCount();
        $lang = 'projects::filament/pages/assign-project-managers';
    @endphp

    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">
                {{ __($lang.'.title') }}
            </x-slot>

            <x-slot name="description">
                {{ __($lang.'.description') }}
            </x-slot>

            {{-- Workload summary chips: click to filter by manager --}}
            <div class="mb-4 flex flex-wrap gap-2">
                <button type="button" wire:click="setManagerFilter('all')"
                    class="rounded-full px-3 py-1.5 text-xs font-medium {{ $managerFilter === 'all' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-200' }}">
                    {{ __($lang.'.summary.all') }}
                </button>
                <button type="button" wire:click="setManagerFilter('unassigned')"
                    class="rounded-full px-3 py-1.5 text-xs font-medium {{ $managerFilter === 'unassigned' ? 'bg-warning-600 text-white' : 'bg-warning-50 text-warning-700 dark:bg-warning-400/10 dark:text-warning-400' }}">
                    {{ __($lang.'.summary.unassigned') }} ({{ $unassigned }})
                </button>
                @foreach ($managers as $manager)
                    <button type="button" wire:click="setManagerFilter('user:{{ $manager['id'] }}')"
                        class="rounded-full px-3 py-1.5 text-xs font-medium {{ $managerFilter === 'user:'.$manager['id'] ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-200' }}">
                        {{ $manager['name'] }} ({{ $manager['projects'] }})
                    </button>
                @endforeach
            </div>

            <div class="mb-4">
                <input type="text" wire:model.live.debounce.500ms="search"
                    placeholder="{{ __($lang.'.search-placeholder') }}"
                    class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-gray-100 sm:max-w-sm">
            </div>

            @if (empty($rows))
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __($lang.'.table.empty') }}
                </p>
            @else
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-white/10">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10">
                        <thead class="bg-gray-50 dark:bg-white/5">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __($lang.'.table.project') }}
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __($lang.'.table.customer') }}
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __($lang.'.table.members') }}
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __($lang.'.table.end-date') }}
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __($lang.'.table.manager') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                            @foreach ($rows as $row)
                                <tr wire:key="project-row-{{ $row['id'] }}">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $row['name'] }}
                                        @if ($row['user_id'] === null)
                                            <span class="ml-2 rounded-full bg-warning-50 px-2 py-0.5 text-xs font-medium text-warning-700 dark:bg-warning-400/10 dark:text-warning-400">
                                                {{ __($lang.'.table.no-manager') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $row['customer'] }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $row['members'] }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $row['end_date'] }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <select wire:change="assignManager({{ $row['id'] }}, $event.target.value)"
                                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-gray-100 sm:max-w-xs">
                                            <option value="" @selected($row['user_id'] === null)>
                                                — {{ __($lang.'.table.no-manager') }} —
                                            </option>
                                            @foreach ($managers as $manager)
                                                <option value="{{ $manager['id'] }}" @selected($row['user_id'] === $manager['id'])>
                                                    {{ $manager['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                {{ __($lang.'.note') }}
            </p>
        </x-filament::section>
    </div>
</x-filament-panels::page>
