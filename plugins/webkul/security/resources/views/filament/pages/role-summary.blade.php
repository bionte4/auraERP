@php
    $cards = $this->roleCards();
    $visibleRoles = $this->visibleRoles();
    $matrix = $this->matrix();
    $poolTotal = $cards[0]['poolTotal'] ?? 0;

    $cellClass = function (int $granted, int $total): string {
        if ($total === 0 || $granted === 0) {
            return 'text-gray-400 dark:text-gray-500';
        }

        if ($granted === $total) {
            return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-400/10 dark:text-emerald-300 font-semibold';
        }

        return 'bg-amber-100 text-amber-800 dark:bg-amber-400/10 dark:text-amber-300 font-medium';
    };
@endphp

<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                        {{ __('security::filament/pages/role-summary.intro.heading') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                        {{ __('security::filament/pages/role-summary.intro.description', ['total' => $poolTotal]) }}
                    </p>
                </div>

                <div class="w-full md:w-80">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('security::filament/pages/role-summary.search.placeholder') }}"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    >
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($cards as $card)
                @php $isSelected = in_array($card['id'], $selectedRoleIds, true); @endphp
                <div class="rounded-xl border p-5 shadow-sm transition {{ $isSelected ? 'border-primary-500 bg-primary-50 ring-1 ring-primary-500 dark:border-primary-400 dark:bg-primary-400/10' : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900' }}">
                    <button type="button" wire:click="selectRole({{ $card['id'] }})" class="block w-full text-left">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-sm font-semibold text-gray-950 dark:text-white">{{ $card['name'] }}</span>
                            <span class="flex items-center gap-1">
                                <span class="rounded-full px-2 py-0.5 text-[11px] font-medium {{ $card['baseline'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300' }}">
                                    {{ $card['baseline'] ? __('security::filament/pages/role-summary.card.baseline') : __('security::filament/pages/role-summary.card.custom') }}
                                </span>
                                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">{{ $card['guard'] }}</span>
                            </span>
                        </div>

                        <div class="mt-3 text-2xl font-bold text-gray-950 dark:text-white">
                            {{ $card['total'] }}<span class="text-sm font-medium text-gray-500"> / {{ $card['poolTotal'] }}</span>
                            <span class="text-sm font-medium text-gray-500">({{ $card['percentage'] }}%)</span>
                        </div>

                        <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-full rounded-full bg-primary-500" style="width: {{ $card['percentage'] }}%"></div>
                        </div>

                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            {{ __('security::filament/pages/role-summary.card.permissions') }} · {{ $card['users'] }} {{ __('security::filament/pages/role-summary.card.users') }}
                        </div>
                    </button>

                    <dl class="mt-4 grid grid-cols-4 gap-2 text-center">
                        <div class="rounded-lg bg-gray-50 px-1 py-2 dark:bg-gray-800">
                            <dt class="text-[10px] uppercase tracking-wide text-gray-500">{{ __('security::filament/pages/role-summary.card.resources') }}</dt>
                            <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ $card['resources'] }}</dd>
                        </div>
                        <div class="rounded-lg bg-gray-50 px-1 py-2 dark:bg-gray-800">
                            <dt class="text-[10px] uppercase tracking-wide text-gray-500">{{ __('security::filament/pages/role-summary.card.pages') }}</dt>
                            <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ $card['pages'] }}</dd>
                        </div>
                        <div class="rounded-lg bg-gray-50 px-1 py-2 dark:bg-gray-800">
                            <dt class="text-[10px] uppercase tracking-wide text-gray-500">{{ __('security::filament/pages/role-summary.card.widgets') }}</dt>
                            <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ $card['widgets'] }}</dd>
                        </div>
                        <div class="rounded-lg bg-gray-50 px-1 py-2 dark:bg-gray-800" title="{{ __('security::filament/pages/role-summary.card.other-hint') }}">
                            <dt class="text-[10px] uppercase tracking-wide text-gray-500">{{ __('security::filament/pages/role-summary.card.other') }}</dt>
                            <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ $card['other'] }}</dd>
                        </div>
                    </dl>

                    @if ($card['userNames'] !== [])
                        <details class="mt-3">
                            <summary class="cursor-pointer text-xs font-medium text-primary-600 hover:underline dark:text-primary-400">
                                {{ __('security::filament/pages/role-summary.card.show-users') }} ({{ $card['users'] }})
                            </summary>
                            <ul class="mt-1 list-inside list-disc text-xs text-gray-600 dark:text-gray-300">
                                @foreach ($card['userNames'] as $userName)
                                    <li>{{ $userName }}</li>
                                @endforeach
                            </ul>
                        </details>
                    @endif

                    @if ($card['editUrl'])
                        <a href="{{ $card['editUrl'] }}" class="mt-3 inline-block text-xs font-medium text-primary-600 hover:underline dark:text-primary-400">
                            {{ __('security::filament/pages/role-summary.card.edit') }} →
                        </a>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="flex flex-wrap items-center gap-4 rounded-xl border border-gray-200 bg-white px-5 py-3 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            @if ($selectedRoleIds !== [])
                <span class="text-sm text-gray-700 dark:text-gray-200">
                    {{ __('security::filament/pages/role-summary.compare.selected', ['count' => count($selectedRoleIds)]) }}
                </span>
                <button type="button" wire:click="clearSelection" class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-400">
                    {{ __('security::filament/pages/role-summary.compare.clear') }}
                </button>
            @else
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('security::filament/pages/role-summary.compare.hint') }}</span>
            @endif

            <label class="ml-auto flex cursor-pointer items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                <input type="checkbox" wire:model.live="onlyDifferences" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800">
                {{ __('security::filament/pages/role-summary.compare.only-differences') }}
            </label>
        </div>

        @forelse ($matrix as $category => $groups)
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                        {{ __('security::filament/pages/role-summary.categories.'.$category) }}
                    </h3>
                </div>

                @foreach ($groups as $group => $rows)
                    <div class="px-5 py-4" x-data="{ open: {{ trim($search) !== '' ? 'true' : 'false' }} }">
                        <button type="button" x-on:click="open = ! open" class="mb-3 flex w-full items-center gap-2 text-left">
                            <svg class="h-4 w-4 text-gray-400 transition-transform" x-bind:class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                            <span class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $group }}</span>
                            <span class="text-xs text-gray-400">({{ count($rows) }})</span>
                        </button>

                        <div class="max-h-[32rem] overflow-auto" x-show="open" x-cloak>
                            <table class="w-full min-w-[640px] text-sm">
                                <thead>
                                    <tr class="text-left text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                        <th class="sticky top-0 z-10 bg-white pb-2 pr-4 font-medium dark:bg-gray-900">{{ __('security::filament/pages/role-summary.table.item') }}</th>
                                        @foreach ($visibleRoles as $role)
                                            <th class="sticky top-0 z-10 bg-white pb-2 pr-4 font-medium dark:bg-gray-900">{{ $role->name }}</th>
                                        @endforeach
                                    </tr>
                                </thead>

                                @foreach ($rows as $row)
                                    <tbody x-data="{ open: false }">
                                        <tr class="border-t border-gray-100 dark:border-gray-800">
                                            <td class="py-2 pr-4">
                                                <button type="button" x-on:click="open = ! open" class="font-medium text-gray-900 hover:text-primary-600 dark:text-white">
                                                    {{ $row['label'] }}
                                                </button>
                                                <span class="ml-1 text-xs text-gray-400">({{ $row['total'] }})</span>
                                            </td>
                                            @foreach ($visibleRoles as $role)
                                                @php $grant = $row['grants'][$role->id] ?? ['count' => 0, 'names' => []]; @endphp
                                                <td class="py-2 pr-4">
                                                    <span class="inline-block rounded-md px-2 py-0.5 text-xs {{ $cellClass($grant['count'], $row['total']) }}">
                                                        {{ $grant['count'] }}/{{ $row['total'] }}
                                                    </span>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr x-show="open" x-cloak class="border-t border-gray-50 dark:border-gray-800/60">
                                            <td colspan="{{ 1 + $visibleRoles->count() }}" class="py-3 pr-4">
                                                <div class="space-y-3">
                                                    @foreach ($visibleRoles as $role)
                                                        @php $grant = $row['grants'][$role->id] ?? ['count' => 0, 'names' => []]; @endphp
                                                        @if ($grant['count'] > 0)
                                                            <div>
                                                                <div class="mb-1 text-xs font-semibold text-gray-700 dark:text-gray-200">{{ $role->name }}</div>
                                                                <div class="flex flex-wrap gap-1">
                                                                    @foreach ($grant['names'] as $permissionName)
                                                                        <span class="rounded bg-gray-100 px-1.5 py-0.5 font-mono text-[11px] text-gray-700 dark:bg-gray-800 dark:text-gray-200">{{ $permissionName }}</span>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                @endforeach
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @empty
            <div class="rounded-xl border border-gray-200 bg-white p-8 text-center text-sm text-gray-500 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400">
                {{ __('security::filament/pages/role-summary.empty') }}
            </div>
        @endforelse
    </div>
</x-filament-panels::page>
