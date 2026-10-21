<x-filament-panels::page>
    @php
        $budget = $this->getBudgetData();
        $rows = $budget['rows'];
        $totals = $budget['totals'];
        $lang = 'projects::filament/pages/project-budget-control';
        $money = fn ($value) => 'Rp '.number_format((float) $value, 0, ',', '.');
        $statusStyles = [
            'ok' => 'bg-success-50 text-success-700 dark:bg-success-400/10 dark:text-success-400',
            'warning' => 'bg-warning-50 text-warning-700 dark:bg-warning-400/10 dark:text-warning-400',
            'over' => 'bg-danger-50 text-danger-700 dark:bg-danger-400/10 dark:text-danger-400',
            'no_budget' => 'bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-gray-300',
        ];
    @endphp

    <div class="space-y-6">
        {{-- Summary cards --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
            @foreach ([
                ['label' => __($lang.'.summary.contract'), 'value' => $money($totals['contract_value'])],
                ['label' => __($lang.'.summary.invoiced'), 'value' => $money($totals['invoiced'])],
                ['label' => __($lang.'.summary.unpaid'), 'value' => $money($totals['unpaid'])],
                ['label' => __($lang.'.summary.vendor-cost'), 'value' => $money($totals['vendor_cost'])],
                ['label' => __($lang.'.summary.attention'), 'value' => ($totals['warning'] + $totals['over']).' ('.$totals['over'].' '.__($lang.'.status.over').')'],
            ] as $card)
                <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-white/10 dark:bg-white/5">
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $card['label'] }}</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $card['value'] }}</p>
                </div>
            @endforeach
        </div>

        <x-filament::section>
            <x-slot name="heading">
                {{ __($lang.'.title') }}
            </x-slot>

            <x-slot name="description">
                {{ __($lang.'.description') }}
            </x-slot>

            <div class="mb-4 flex flex-wrap items-center gap-2">
                @foreach (['all', 'ok', 'warning', 'over', 'no_budget'] as $status)
                    <button type="button" wire:click="setStatusFilter('{{ $status }}')"
                        class="rounded-full px-3 py-1.5 text-xs font-medium {{ $statusFilter === $status ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-200' }}">
                        {{ __($lang.'.status.'.$status) }}
                    </button>
                @endforeach

                <input type="text" wire:model.live.debounce.500ms="search"
                    placeholder="{{ __($lang.'.search-placeholder') }}"
                    class="ml-auto block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-gray-100 sm:max-w-xs">
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
                                @foreach (['project', 'customer', 'contract', 'invoiced', 'budget', 'vendor-cost', 'remaining', 'absorption'] as $column)
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        {{ __($lang.'.table.'.$column) }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                            @foreach ($rows as $row)
                                <tr wire:key="budget-row-{{ $row['id'] }}">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $row['name'] }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $row['customer'] }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $money($row['contract_value']) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $money($row['invoiced']) }}
                                        @if ($row['invoiced_pct'] !== null)
                                            <span class="text-xs text-gray-400">({{ $row['invoiced_pct'] }}%)</span>
                                        @endif
                                        @if ($row['unpaid'] > 0)
                                            <div class="text-xs text-danger-600 dark:text-danger-400">
                                                {{ __($lang.'.table.unpaid') }}: {{ $money($row['unpaid']) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                        @if ($this->canEditBudgets() && $editingBudgetProjectId === $row['id'])
                                            <div class="flex items-center gap-1">
                                                <input type="number" wire:model="budgetAmount" min="0" step="any"
                                                    class="block w-36 rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-gray-100">
                                                <button type="button" wire:click="saveBudget"
                                                    class="rounded-lg bg-primary-600 px-2 py-1.5 text-xs font-medium text-white">
                                                    {{ __($lang.'.actions.save') }}
                                                </button>
                                                <button type="button" wire:click="cancelEditBudget"
                                                    class="rounded-lg bg-gray-100 px-2 py-1.5 text-xs font-medium text-gray-700 dark:bg-white/10 dark:text-gray-200">
                                                    {{ __($lang.'.actions.cancel') }}
                                                </button>
                                            </div>
                                            @error('budgetAmount')
                                                <p class="mt-1 text-xs text-danger-600 dark:text-danger-400">{{ $message }}</p>
                                            @enderror
                                        @else
                                            {{ $row['budget'] > 0 ? $money($row['budget']) : '—' }}
                                            @if ($this->canEditBudgets())
                                                <button type="button" wire:click="startEditBudget({{ $row['id'] }})"
                                                    title="{{ __($lang.'.actions.set-budget') }}"
                                                    class="ml-1 rounded bg-gray-100 px-1.5 py-0.5 text-xs font-medium text-primary-700 hover:bg-gray-200 dark:bg-white/10 dark:text-primary-300 dark:hover:bg-white/20">
                                                    {{ $row['budget'] > 0 ? __($lang.'.actions.edit-budget') : __($lang.'.actions.set-budget') }}
                                                </button>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $money($row['vendor_cost']) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm {{ $row['budget'] > 0 && $row['remaining'] < 0 ? 'font-medium text-danger-600 dark:text-danger-400' : 'text-gray-600 dark:text-gray-300' }}">
                                        {{ $row['budget'] > 0 ? $money($row['remaining']) : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @if ($row['absorption_pct'] !== null)
                                            <span class="mr-1 text-gray-700 dark:text-gray-200">{{ $row['absorption_pct'] }}%</span>
                                        @endif
                                        <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $statusStyles[$row['status']] }}">
                                            {{ __($lang.'.status.'.$row['status']) }}
                                        </span>
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
