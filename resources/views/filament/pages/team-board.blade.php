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
                    <article class="team-card">
                        <header class="team-card-head">
                            <h3><a href="{{ $card['href'] }}">{{ $card['name'] }}</a></h3>
                            @if ($card['is_new'])
                                <span class="team-badge">{{ $labels['new'] }}</span>
                            @endif
                        </header>

                        <p class="team-members-label">Total {{ $card['member_count'] }} {{ $labels['members'] }}</p>
                        <div class="team-avatars">
                            @foreach ($card['members'] as $member)
                                <span class="team-avatar" style="background: {{ $member['color'] }}" title="{{ $member['name'] }}">{{ $member['initials'] }}</span>
                            @endforeach
                            @if ($card['extra_members'] > 0)
                                <span class="team-avatar is-more">+{{ $card['extra_members'] }}</span>
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
