@php
    $scheduleAt = $scheduleAt ?? '02:00';
    $retentionDays = $retentionDays ?? 14;
    $retentionCount = $retentionCount ?? 30;
    $enabled = $enabled ?? true;
    $directory = $directory ?? '';
    $restoreAllowed = $restoreAllowed ?? false;
    $health = $health ?? [];
@endphp

<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                {{ __('security::filament/pages/manage-database-backups.info.heading') }}
            </h3>
            <ul class="mt-3 list-disc space-y-1 pl-5 text-sm text-gray-600 dark:text-gray-300">
                <li>
                    {{ __('security::filament/pages/manage-database-backups.info.enabled', [
                        'status' => $enabled
                            ? __('security::filament/pages/manage-database-backups.info.on')
                            : __('security::filament/pages/manage-database-backups.info.off'),
                    ]) }}
                </li>
                <li>
                    {{ __('security::filament/pages/manage-database-backups.info.schedule', ['time' => $scheduleAt]) }}
                </li>
                <li>
                    {{ __('security::filament/pages/manage-database-backups.info.retention', [
                        'days' => $retentionDays,
                        'count' => $retentionCount,
                    ]) }}
                </li>
                <li>
                    {{ __('security::filament/pages/manage-database-backups.info.directory', ['path' => $directory]) }}
                </li>
                <li>
                    {{ __('security::filament/pages/manage-database-backups.info.restore', [
                        'status' => $restoreAllowed
                            ? __('security::filament/pages/manage-database-backups.info.on')
                            : __('security::filament/pages/manage-database-backups.info.off'),
                    ]) }}
                </li>
                <li>
                    {{ __('security::filament/pages/manage-database-backups.info.health', [
                        'status' => ($health['ok'] ?? false)
                            ? __('security::filament/pages/manage-database-backups.info.health-ok')
                            : __('security::filament/pages/manage-database-backups.info.health-warn'),
                        'last' => $health['last_backup_at'] ?? '—',
                        'free' => $health['disk_free_mb'] ?? '—',
                    ]) }}
                </li>
                <li>
                    {{ __('security::filament/pages/manage-database-backups.info.cron') }}
                </li>
            </ul>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="w-full min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">
                                {{ __('security::filament/pages/manage-database-backups.table.filename') }}
                            </th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">
                                {{ __('security::filament/pages/manage-database-backups.table.created-at') }}
                            </th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">
                                {{ __('security::filament/pages/manage-database-backups.table.size') }}
                            </th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">
                                {{ __('security::filament/pages/manage-database-backups.table.method') }}
                            </th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-300">
                                {{ __('security::filament/pages/manage-database-backups.table.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($backups as $backup)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-950 dark:text-white">
                                    {{ $backup['filename'] }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                    {{ $backup['created_at'] }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                    {{ $backup['size_label'] }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                    {{ $backup['method'] }} / {{ $backup['driver'] }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <x-filament::button
                                            size="sm"
                                            color="gray"
                                            tag="a"
                                            href="{{ route('security.database-backups.download', ['filename' => $backup['filename']]) }}"
                                        >
                                            {{ __('security::filament/pages/manage-database-backups.actions.download') }}
                                        </x-filament::button>
                                        @if ($restoreAllowed)
                                            <x-filament::button
                                                size="sm"
                                                color="warning"
                                                wire:click="requestRestore({{ \Illuminate\Support\Js::from($backup['filename']) }})"
                                                wire:confirm="{{ __('security::filament/pages/manage-database-backups.actions.restore-preconfirm') }}"
                                            >
                                                {{ __('security::filament/pages/manage-database-backups.actions.restore') }}
                                            </x-filament::button>
                                        @endif
                                        <x-filament::button
                                            size="sm"
                                            color="danger"
                                            wire:click="deleteBackup({{ \Illuminate\Support\Js::from($backup['filename']) }})"
                                            wire:confirm="{{ __('security::filament/pages/manage-database-backups.actions.delete-confirm') }}"
                                        >
                                            {{ __('security::filament/pages/manage-database-backups.actions.delete') }}
                                        </x-filament::button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    {{ __('security::filament/pages/manage-database-backups.table.empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
