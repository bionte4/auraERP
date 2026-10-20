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

    $roleStyles = [
        'admin'             => ['icon' => 'heroicon-o-shield-check', 'badge' => 'bg-rose-100 text-rose-600 dark:bg-rose-400/10 dark:text-rose-300'],
        'hrd'               => ['icon' => 'heroicon-o-user-group', 'badge' => 'bg-violet-100 text-violet-600 dark:bg-violet-400/10 dark:text-violet-300'],
        'operation manager' => ['icon' => 'heroicon-o-cog-6-tooth', 'badge' => 'bg-sky-100 text-sky-600 dark:bg-sky-400/10 dark:text-sky-300'],
        'accounting'        => ['icon' => 'heroicon-o-calculator', 'badge' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-300'],
        'kasir'             => ['icon' => 'heroicon-o-shopping-cart', 'badge' => 'bg-amber-100 text-amber-600 dark:bg-amber-400/10 dark:text-amber-300'],
        'team leader'       => ['icon' => 'heroicon-o-flag', 'badge' => 'bg-indigo-100 text-indigo-600 dark:bg-indigo-400/10 dark:text-indigo-300'],
        'staff'             => ['icon' => 'heroicon-o-user', 'badge' => 'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-300'],
    ];
    $defaultStyle = ['icon' => 'heroicon-o-identification', 'badge' => 'bg-teal-100 text-teal-600 dark:bg-teal-400/10 dark:text-teal-300'];

    $initials = function (string $name): string {
        $parts = preg_split('/\s+/', trim($name)) ?: [];

        return mb_strtoupper(mb_substr($parts[0] ?? '?', 0, 1) . mb_substr($parts[1] ?? '', 0, 1));
    };
@endphp

<x-filament-panels::page>
    <div class="space-y-4">
        {{-- Compact header --}}
        <div class="flex flex-col gap-3 rounded-xl bg-white px-4 py-3 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 md:flex-row md:items-center">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-400/10 dark:text-primary-400">
                    <x-filament::icon icon="heroicon-o-clipboard-document-list" class="h-5 w-5" />
                </span>
                <div>
                    <h3 class="text-sm font-semibold text-gray-950 dark:text-white">
                        {{ __('security::filament/pages/role-summary.intro.heading') }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ __('security::filament/pages/role-summary.intro.description', ['total' => $poolTotal]) }}
                    </p>
                </div>
            </div>

            <div class="w-full md:ml-auto md:w-72">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('security::filament/pages/role-summary.search.placeholder') }}"
                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                >
            </div>
        </div>

        {{-- Role cards --}}
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">
            @foreach ($cards as $card)
                @php
                    $isSelected = in_array($card['id'], $selectedRoleIds, true);
                    $style = $roleStyles[mb_strtolower($card['name'])] ?? $defaultStyle;
                @endphp
                <div class="relative rounded-xl bg-white p-4 shadow-sm ring-1 transition dark:bg-gray-900 {{ $isSelected ? 'ring-2 ring-primary-500 dark:ring-primary-400' : 'ring-gray-950/5 hover:ring-primary-300 dark:ring-white/10 dark:hover:ring-primary-400/40' }}">
                    @if ($isSelected)
                        <span class="absolute -right-2 -top-2 flex h-5 w-5 items-center justify-center rounded-full bg-primary-500 text-white">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        </span>
                    @endif

                    <button type="button" wire:click="selectRole({{ $card['id'] }})" class="block w-full text-left">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $style['badge'] }}">
                                <x-filament::icon :icon="$style['icon']" class="h-5 w-5" />
                            </span>
                            <span class="min-w-0">
                                <span class="block truncate text-sm font-semibold text-gray-950 dark:text-white">{{ $card['name'] }}</span>
                                <span class="mt-0.5 flex items-center gap-1">
                                    <span class="rounded-full px-1.5 py-px text-[10px] font-medium {{ $card['baseline'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300' }}">
                                        {{ $card['baseline'] ? __('security::filament/pages/role-summary.card.baseline') : __('security::filament/pages/role-summary.card.custom') }}
                                    </span>
                                    <span class="rounded-full bg-gray-100 px-1.5 py-px text-[10px] font-medium text-gray-500 dark:bg-gray-800 dark:text-gray-400">{{ $card['guard'] }}</span>
                                </span>
                            </span>
                            <span class="ml-auto text-right">
                                <span class="block text-lg font-bold leading-none text-gray-950 dark:text-white">{{ $card['total'] }}</span>
                                <span class="text-[11px] text-gray-400">/ {{ $card['poolTotal'] }} · {{ $card['percentage'] }}%</span>
                            </span>
                        </div>

                        <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-full rounded-full bg-primary-500" style="width: {{ $card['percentage'] }}%"></div>
                        </div>

                        <div class="mt-2.5 flex flex-wrap gap-1 text-[11px] text-gray-600 dark:text-gray-300">
                            <span class="rounded bg-gray-50 px-1.5 py-0.5 dark:bg-gray-800">{{ __('security::filament/pages/role-summary.card.resources') }} <strong>{{ $card['resources'] }}</strong></span>
                            <span class="rounded bg-gray-50 px-1.5 py-0.5 dark:bg-gray-800">{{ __('security::filament/pages/role-summary.card.pages') }} <strong>{{ $card['pages'] }}</strong></span>
                            <span class="rounded bg-gray-50 px-1.5 py-0.5 dark:bg-gray-800">{{ __('security::filament/pages/role-summary.card.widgets') }} <strong>{{ $card['widgets'] }}</strong></span>
                            <span class="rounded bg-gray-50 px-1.5 py-0.5 dark:bg-gray-800" title="{{ __('security::filament/pages/role-summary.card.other-hint') }}">{{ __('security::filament/pages/role-summary.card.other') }} <strong>{{ $card['other'] }}</strong></span>
                        </div>
                    </button>

                    <div class="mt-3 flex items-center gap-2 border-t border-gray-100 pt-3 dark:border-gray-800">
                        @if ($card['userNames'] !== [])
                            <details class="relative">
                                <summary class="flex cursor-pointer list-none items-center gap-2 [&::-webkit-details-marker]:hidden">
                                    <span class="flex -space-x-1.5">
                                        @foreach (array_slice($card['userNames'], 0, 4) as $userName)
                                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-primary-100 text-[9px] font-bold text-primary-700 ring-2 ring-white dark:bg-primary-400/20 dark:text-primary-300 dark:ring-gray-900">{{ $initials($userName) }}</span>
                                        @endforeach
                                    </span>
                                    <span class="text-xs text-gray-500 hover:text-primary-600 dark:text-gray-400">{{ $card['users'] }} {{ __('security::filament/pages/role-summary.card.users') }}</span>
                                </summary>
                                <ul class="absolute left-0 top-8 z-20 max-h-48 w-52 overflow-auto rounded-lg bg-white p-2 text-xs text-gray-700 shadow-lg ring-1 ring-gray-950/10 dark:bg-gray-800 dark:text-gray-200 dark:ring-white/10">
                                    @foreach ($card['userNames'] as $userName)
                                        <li class="rounded px-1.5 py-1 hover:bg-gray-50 dark:hover:bg-gray-700">{{ $userName }}</li>
                                    @endforeach
                                </ul>
                            </details>
                        @else
                            <span class="text-xs text-gray-400">0 {{ __('security::filament/pages/role-summary.card.users') }}</span>
                        @endif

                        @if ($card['editUrl'])
                            <a href="{{ $card['editUrl'] }}" class="ml-auto rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700 transition hover:bg-primary-50 hover:text-primary-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-primary-400/10 dark:hover:text-primary-400">
                                {{ __('security::filament/pages/role-summary.card.edit') }} →
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Sticky compare bar --}}
        <div class="sticky top-16 z-30 flex flex-wrap items-center gap-2 rounded-xl bg-white/95 px-4 py-2.5 shadow-sm ring-1 ring-gray-950/5 backdrop-blur dark:bg-gray-900/95 dark:ring-white/10">
            @if ($selectedRoleIds !== [])
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('security::filament/pages/role-summary.compare.selected', ['count' => count($selectedRoleIds)]) }}:</span>
                @foreach ($visibleRoles as $role)
                    <button type="button" wire:click="selectRole({{ $role->id }})" class="group flex items-center gap-1 rounded-full bg-primary-50 py-0.5 pl-2.5 pr-1.5 text-xs font-medium text-primary-700 ring-1 ring-primary-200 transition hover:bg-primary-100 dark:bg-primary-400/10 dark:text-primary-300 dark:ring-primary-400/30">
                        {{ $role->name }}
                        <span class="flex h-4 w-4 items-center justify-center rounded-full group-hover:bg-primary-200 dark:group-hover:bg-primary-400/20">×</span>
                    </button>
                @endforeach
                <button type="button" wire:click="clearSelection" class="text-xs font-medium text-gray-500 hover:text-primary-600 hover:underline dark:text-gray-400">
                    {{ __('security::filament/pages/role-summary.compare.clear') }}
                </button>
            @else
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('security::filament/pages/role-summary.compare.hint') }}</span>
            @endif

            <label class="ml-auto flex cursor-pointer items-center gap-2 text-xs font-medium text-gray-700 dark:text-gray-200">
                <input type="checkbox" wire:model.live="onlyDifferences" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800">
                {{ __('security::filament/pages/role-summary.compare.only-differences') }}
            </label>
        </div>

        {{-- Matrix --}}
        @forelse ($matrix as $category => $groups)
            <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="border-b border-gray-100 px-4 py-3 dark:border-gray-800">
                    <h3 class="text-sm font-semibold text-gray-950 dark:text-white">
                        {{ __('security::filament/pages/role-summary.categories.'.$category) }}
                    </h3>
                </div>

                @foreach ($groups as $group => $rows)
                    <div class="px-4 py-3" x-data="{ open: {{ trim($search) !== '' ? 'true' : 'false' }} }">
                        <button type="button" x-on:click="open = ! open" class="mb-2 flex w-full items-center gap-2 text-left">
                            <svg class="h-4 w-4 text-gray-400 transition-transform" x-bind:class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $group }}</span>
                            <span class="rounded-full bg-gray-100 px-1.5 py-px text-[10px] font-medium text-gray-500 dark:bg-gray-800 dark:text-gray-400">{{ count($rows) }}</span>
                        </button>

                        <div class="max-h-[32rem] overflow-auto rounded-lg ring-1 ring-gray-100 dark:ring-gray-800" x-show="open" x-cloak>
                            <table class="w-full min-w-[640px] text-[13px]">
                                <thead>
                                    <tr class="text-left text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                        <th class="sticky left-0 top-0 z-20 min-w-44 bg-gray-50 px-3 py-2 font-medium dark:bg-gray-800">{{ __('security::filament/pages/role-summary.table.item') }}</th>
                                        @foreach ($visibleRoles as $role)
                                            <th class="sticky top-0 z-10 bg-gray-50 px-3 py-2 font-medium dark:bg-gray-800">{{ $role->name }}</th>
                                        @endforeach
                                    </tr>
                                </thead>

                                @foreach ($rows as $row)
                                    <tbody x-data="{ open: false }">
                                        <tr class="border-t border-gray-100 dark:border-gray-800 {{ $loop->even ? 'bg-gray-50/60 dark:bg-gray-800/30' : '' }}">
                                            <td class="sticky left-0 z-10 bg-inherit px-3 py-1.5">
                                                <button type="button" x-on:click="open = ! open" class="font-medium text-gray-900 hover:text-primary-600 dark:text-white">
                                                    {{ $row['label'] }}
                                                </button>
                                                <span class="ml-1 text-[11px] text-gray-400">({{ $row['total'] }})</span>
                                            </td>
                                            @foreach ($visibleRoles as $role)
                                                @php $grant = $row['grants'][$role->id] ?? ['count' => 0, 'names' => []]; @endphp
                                                <td class="px-3 py-1.5">
                                                    <span class="inline-block rounded px-1.5 py-px text-[11px] {{ $cellClass($grant['count'], $row['total']) }}">
                                                        {{ $grant['count'] }}/{{ $row['total'] }}
                                                    </span>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr x-show="open" x-cloak class="border-t border-gray-50 dark:border-gray-800/60">
                                            <td colspan="{{ 1 + $visibleRoles->count() }}" class="px-3 py-2.5">
                                                <div class="space-y-2.5">
                                                    @foreach ($visibleRoles as $role)
                                                        @php $grant = $row['grants'][$role->id] ?? ['count' => 0, 'names' => []]; @endphp
                                                        @if ($grant['count'] > 0)
                                                            <div>
                                                                <div class="mb-1 text-[11px] font-semibold text-gray-700 dark:text-gray-200">{{ $role->name }}</div>
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
            <div class="rounded-xl bg-white p-8 text-center text-sm text-gray-500 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:text-gray-400 dark:ring-white/10">
                {{ __('security::filament/pages/role-summary.empty') }}
            </div>
        @endforelse
    </div>
</x-filament-panels::page>
