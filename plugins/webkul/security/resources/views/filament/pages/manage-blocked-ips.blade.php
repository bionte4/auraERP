@php
    $enabled = $enabled ?? true;
    $maxAttempts = $maxAttempts ?? 5;
    $decayMinutes = $decayMinutes ?? 1;
    $blockAfterFailures = $blockAfterFailures ?? 10;
    $failureWindow = $failureWindow ?? 15;
    $blockMinutes = $blockMinutes ?? 30;
    $whitelist = $whitelist ?? '';
@endphp

<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                {{ __('security::filament/pages/manage-blocked-ips.info.heading') }}
            </h3>
            <ul class="mt-3 list-disc space-y-1 pl-5 text-sm text-gray-600 dark:text-gray-300">
                <li>
                    {{ __('security::filament/pages/manage-blocked-ips.info.enabled', [
                        'status' => $enabled
                            ? __('security::filament/pages/manage-blocked-ips.info.on')
                            : __('security::filament/pages/manage-blocked-ips.info.off'),
                    ]) }}
                </li>
                <li>
                    {{ __('security::filament/pages/manage-blocked-ips.info.rate-limit', [
                        'attempts' => $maxAttempts,
                        'minutes' => $decayMinutes,
                    ]) }}
                </li>
                <li>
                    {{ __('security::filament/pages/manage-blocked-ips.info.ip-block', [
                        'failures' => $blockAfterFailures,
                        'window' => $failureWindow,
                        'minutes' => $blockMinutes,
                    ]) }}
                </li>
                <li>
                    {{ __('security::filament/pages/manage-blocked-ips.info.whitelist', [
                        'ips' => $whitelist !== '' ? $whitelist : '—',
                    ]) }}
                </li>
            </ul>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="w-full min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">
                                {{ __('security::filament/pages/manage-blocked-ips.table.ip') }}
                            </th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">
                                {{ __('security::filament/pages/manage-blocked-ips.table.attempts') }}
                            </th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">
                                {{ __('security::filament/pages/manage-blocked-ips.table.reason') }}
                            </th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">
                                {{ __('security::filament/pages/manage-blocked-ips.table.last-email') }}
                            </th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">
                                {{ __('security::filament/pages/manage-blocked-ips.table.blocked-until') }}
                            </th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-300">
                                {{ __('security::filament/pages/manage-blocked-ips.table.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($blockedIps as $row)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-950 dark:text-white">
                                    {{ $row['ip_address'] }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                    {{ $row['failed_attempts'] }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                    {{ $row['reason'] ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                    {{ $row['last_email'] ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                    {{ $row['blocked_until'] ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <x-filament::button
                                            size="sm"
                                            color="success"
                                            wire:click="unblockIp({{ \Illuminate\Support\Js::from($row['ip_address']) }})"
                                            wire:confirm="{{ __('security::filament/pages/manage-blocked-ips.actions.unblock-confirm') }}"
                                        >
                                            {{ __('security::filament/pages/manage-blocked-ips.actions.unblock') }}
                                        </x-filament::button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    {{ __('security::filament/pages/manage-blocked-ips.table.empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
