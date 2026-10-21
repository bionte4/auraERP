@php
    $board = $this->getBoardData();
    $labels = $board['labels'];
@endphp

<x-filament-panels::page>
    <div class="team-board">
        <div class="team-board-head">
            <div>
                <h2>{{ $board['title'] }}</h2>
                <p>{{ $board['subtitle'] }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @foreach (['cards', 'compact', 'list'] as $mode)
                    <button
                        type="button"
                        wire:click="setViewMode('{{ $mode }}')"
                        class="rounded-lg px-3 py-1.5 text-sm {{ ($board['viewMode'] ?? 'cards') === $mode ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200' }}"
                    >
                        {{ $labels['mode-'.$mode] }}
                    </button>
                @endforeach
                @if ($board['manages'])
                    <button
                        type="button"
                        wire:click="setViewMode('kanban')"
                        class="rounded-lg px-3 py-1.5 text-sm {{ ($board['viewMode'] ?? '') === 'kanban' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200' }}"
                    >
                        {{ $labels['mode-kanban'] }}
                    </button>
                @endif
                @if (($board['viewMode'] ?? 'cards') !== 'kanban')
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ $labels['search-placeholder'] }}"
                        class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 sm:max-w-[220px]"
                    >
                    @php
                        $sortChoices = ['name_asc', 'name_desc', 'members_desc', 'projects_desc', 'tasks_desc', 'progress_desc', 'progress_asc', ...(($board['canViewValue'] ?? false) ? ['value_desc'] : []), 'updated_desc'];
                    @endphp
                    <select
                        wire:change="setSortBy($event.target.value)"
                        aria-label="{{ $labels['sort'] }}"
                        title="{{ $labels['sort'] }}"
                        class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 sm:max-w-[200px]"
                    >
                        @foreach ($sortChoices as $sortChoice)
                            <option value="{{ $sortChoice }}" @selected(($board['sortBy'] ?? 'name_asc') === $sortChoice)>{{ $labels['sort-'.$sortChoice] }}</option>
                        @endforeach
                    </select>
                @endif
                @if ($board['action'])
                    <a class="team-board-add" href="{{ $board['action']['href'] }}">+ {{ $board['action']['label'] }}</a>
                @endif
            </div>
        </div>

        @if (($board['viewMode'] ?? 'cards') === 'kanban' && $board['manages'])
            @if (($board['kanban'] ?? []) === [])
                <div class="team-empty">{{ $board['empty'] }}</div>
            @else
                <div class="flex gap-4 overflow-x-auto pb-4">
                    @foreach ($board['kanban'] as $column)
                        <div
                            class="min-w-[240px] max-w-[280px] flex-1 rounded-xl border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-900"
                            data-project-id="{{ $column['id'] }}"
                            ondragover="event.preventDefault()"
                            ondrop="
                                event.preventDefault();
                                const employeeId = Number(event.dataTransfer.getData('employeeId'));
                                const fromProjectId = Number(event.dataTransfer.getData('fromProjectId'));
                                const toProjectId = Number('{{ $column['id'] }}');
                                if (employeeId && fromProjectId && toProjectId) {
                                    @this.call('moveEmployeeOnKanban', employeeId, fromProjectId, toProjectId);
                                }
                            "
                        >
                            <div class="mb-3 flex items-center justify-between gap-2">
                                <a href="{{ $column['href'] }}" class="font-semibold text-gray-950 dark:text-white">{{ $column['name'] }}</a>
                                <span class="text-xs text-gray-500">{{ count($column['employees']) }}</span>
                            </div>
                            <div class="space-y-2 min-h-[80px]">
                                @forelse ($column['employees'] as $employee)
                                    <div
                                        class="cursor-grab rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-800"
                                        draggable="true"
                                        ondragstart="
                                            event.dataTransfer.setData('employeeId', '{{ $employee['id'] }}');
                                            event.dataTransfer.setData('fromProjectId', '{{ $column['id'] }}');
                                        "
                                    >
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full text-xs text-white" style="background: {{ $employee['color'] }}">
                                                {{ $employee['initials'] }}
                                            </span>
                                            @if (! empty($employee['href']))
                                                <a href="{{ $employee['href'] }}" class="font-medium text-gray-900 dark:text-gray-100">{{ $employee['name'] }}</a>
                                            @else
                                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $employee['name'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-gray-400">{{ $labels['drop-here'] }}</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @elseif ($board['cards'] === [])
            <div class="team-empty">{{ ($board['searching'] ?? false) ? $labels['no-results'] : $board['empty'] }}</div>
        @elseif (($board['viewMode'] ?? 'cards') === 'list')
            @php $showValue = collect($board['cards'])->contains(fn ($c) => ($c['value'] ?? null) !== null); @endphp
            <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            @foreach (['team', 'members', 'projects', 'tasks', ...($showValue ? ['value'] : []), 'status', 'updated', 'details'] as $column)
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ $labels[$column] }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($board['cards'] as $card)
                            <tr wire:key="team-row-{{ $card['name'] }}">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    <a href="{{ $card['href'] }}" class="hover:underline">{{ $card['name'] }}</a>
                                    @if ($card['is_new'])
                                        <span class="team-badge">{{ $labels['new'] }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    <span class="inline-flex items-center gap-1">
                                        @foreach ($card['members'] as $member)
                                            <span class="team-avatar" style="background: {{ $member['color'] }}" title="{{ $member['name'] }}">{{ $member['initials'] }}</span>
                                        @endforeach
                                        @if ($card['extra_members'] > 0)
                                            <span class="team-avatar is-more">+{{ $card['extra_members'] }}</span>
                                        @endif
                                        <span class="ml-1">{{ $card['member_count'] }}</span>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $card['project_count'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $card['task_count'] }}</td>
                                @if ($showValue)
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $card['value'] ?? '—' }}</td>
                                @endif
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    <div class="flex items-center gap-2">
                                        <div class="team-progress-bar" style="min-width: 70px"><i style="width: {{ $card['progress'] }}%"></i></div>
                                        <span>{{ $card['progress'] }}%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $card['updated'] }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="{{ $card['href'] }}" class="text-primary-600 hover:underline dark:text-primary-400">{{ $labels['details'] }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="team-board-grid {{ ($board['viewMode'] ?? 'cards') === 'compact' ? 'is-compact' : '' }}">
                @foreach ($board['cards'] as $card)
                    <article class="team-card {{ ($board['viewMode'] ?? 'cards') === 'compact' ? 'is-compact' : '' }}" x-data="{ membersOpen: false }">
                        <header class="team-card-head">
                            <h3><a href="{{ $card['href'] }}">{{ $card['name'] }}</a></h3>
                            @if ($card['is_new'])
                                <span class="team-badge">{{ $labels['new'] }}</span>
                            @endif
                        </header>

                        <button
                            type="button"
                            class="team-members-label is-clickable"
                            @click="membersOpen = ! membersOpen"
                            :aria-expanded="membersOpen.toString()"
                            title="{{ $labels['members-click'] }}"
                        >
                            Total {{ $card['member_count'] }} {{ $labels['members'] }}
                            <span class="team-members-toggle" x-text="membersOpen ? '▲' : '▼'"></span>
                        </button>
                        <div class="team-avatars" @click="membersOpen = ! membersOpen" role="button" tabindex="0">
                            @foreach ($card['members'] as $member)
                                <span class="team-avatar" style="background: {{ $member['color'] }}" title="{{ $member['name'] }}">{{ $member['initials'] }}</span>
                            @endforeach
                            @if ($card['extra_members'] > 0)
                                <span class="team-avatar is-more">+{{ $card['extra_members'] }}</span>
                            @endif
                        </div>

                        <div class="team-member-list" x-show="membersOpen" x-cloak x-collapse>
                            <strong>{{ $labels['members-list'] }}</strong>
                            @if (($card['members_all'] ?? []) === [])
                                <p class="team-member-empty">—</p>
                            @else
                                <ul>
                                    @foreach ($card['members_all'] as $member)
                                        <li>
                                            <span class="team-member-dot" style="background: {{ $member['color'] }}"></span>
                                            <div>
                                                @if (! empty($member['href']))
                                                    <a href="{{ $member['href'] }}">{{ $member['name'] }}</a>
                                                @else
                                                    <span class="team-member-name">{{ $member['name'] }}</span>
                                                @endif
                                                <em>{{ $member['role'] }}@if (! empty($member['projects'])) — {{ $member['projects'] }}@endif</em>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <div class="team-about">
                            <strong>{{ $labels['about'] }}</strong>
                            <p>{{ $card['about'] }}</p>
                        </div>

                        <div class="team-stats">
                            <div>
                                <span>{{ $labels['projects'] }}</span>
                                <strong>{{ $card['project_count'] }}</strong>
                            </div>
                            <div>
                                <span>{{ $labels['tasks'] }}</span>
                                <strong>{{ $card['task_count'] }}</strong>
                            </div>
                            @if (($card['value'] ?? null) !== null)
                                <div>
                                    <span>{{ $labels['value'] }}</span>
                                    <strong>{{ $card['value'] }}</strong>
                                </div>
                            @endif
                        </div>

                        @if (($card['projects'] ?? []) !== [])
                            <div class="team-project-list">
                                <strong>{{ $labels['projects'] }}</strong>
                                <ul>
                                    @foreach ($card['projects'] as $project)
                                        <li>
                                            <a href="{{ $project['href'] }}">{{ $project['name'] }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="team-progress">
                            <div class="team-progress-label">
                                <span>{{ $labels['status'] }}</span>
                                <strong>{{ $card['progress'] }}%</strong>
                            </div>
                            <div class="team-progress-bar"><i style="width: {{ $card['progress'] }}%"></i></div>
                        </div>

                        <footer class="team-card-foot">
                            <span>{{ $labels['updated'] }} {{ $card['updated'] }}</span>
                            <a href="{{ $card['href'] }}">{{ $labels['details'] }}</a>
                        </footer>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-panels::page>
