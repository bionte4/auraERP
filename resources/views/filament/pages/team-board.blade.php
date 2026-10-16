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
            @if ($board['action'])
                <a class="team-board-add" href="{{ $board['action']['href'] }}">+ {{ $board['action']['label'] }}</a>
            @endif
        </div>

        @if ($board['cards'] === [])
            <div class="team-empty">{{ $board['empty'] }}</div>
        @else
            <div class="team-board-grid">
                @foreach ($board['cards'] as $card)
                    <article class="team-card" x-data="{ membersOpen: false }">
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
                            <div>
                                <span>{{ $labels['value'] }}</span>
                                <strong>{{ $card['value'] }}</strong>
                            </div>
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
