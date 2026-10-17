@php
    $day = $this->getDayData();
    $indonesian = str_starts_with((string) app()->getLocale(), 'id');
    $toneMap = [
        'primary' => 'is-primary',
        'success' => 'is-success',
        'warning' => 'is-warning',
        'danger'  => 'is-danger',
        'info'    => 'is-info',
        'gray'    => 'is-gray',
    ];
@endphp

<x-filament-panels::page>
    <div class="aura-myday">
        <section class="aura-myday-hero">
            <div class="aura-myday-hero-copy">
                <p class="aura-myday-date">{{ $day['date_label'] }}</p>
                <h2 class="aura-myday-greeting">{{ $day['greeting'] }}</h2>
                <p class="aura-myday-subtitle">{{ $day['subtitle'] }}</p>
            </div>
            <div class="aura-myday-hero-meta">
                <span class="aura-myday-role">{{ $day['role'] }}</span>
                @if (($day['attention_count'] ?? 0) > 0)
                    <span class="aura-myday-attention">
                        {{ $indonesian ? $day['attention_count'].' perlu perhatian' : $day['attention_count'].' need attention' }}
                    </span>
                @else
                    <span class="aura-myday-attention is-clear">
                        {{ $indonesian ? 'Tidak ada alert kritis' : 'No critical alerts' }}
                    </span>
                @endif
            </div>
        </section>

        @if (! empty($day['recommendations']))
            <section class="aura-myday-section">
                <div class="aura-myday-section-head">
                    <h3>{{ $indonesian ? 'Prioritas hari ini' : 'Today\'s priorities' }}</h3>
                    <p>{{ $indonesian ? 'Rekomendasi tindakan berdasarkan data Anda saat ini.' : 'Recommended actions based on your current data.' }}</p>
                </div>

                <div class="aura-myday-priorities">
                    @foreach ($day['recommendations'] as $index => $item)
                        <article @class(['aura-myday-priority', $toneMap[$item['color'] ?? 'primary'] ?? 'is-primary'])>
                            <div class="aura-myday-priority-index">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</div>
                            <div class="aura-myday-priority-body">
                                <h4>{{ $item['title'] }}</h4>
                                <p>{{ $item['body'] }}</p>
                            </div>
                            <a href="{{ $item['href'] }}" class="aura-myday-priority-cta">
                                {{ $item['cta'] }}
                                <span aria-hidden="true">→</span>
                            </a>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="aura-myday-section">
            <div class="aura-myday-section-head">
                <h3>{{ $indonesian ? 'Ringkasan fokus' : 'Focus snapshot' }}</h3>
                <p>{{ $indonesian ? 'Klik kartu untuk langsung ke modul terkait.' : 'Click a card to jump into the related module.' }}</p>
            </div>

            <div class="aura-myday-cards">
                @foreach ($day['cards'] as $card)
                    <a
                        href="{{ $card['href'] }}"
                        @class(['aura-myday-card', $toneMap[$card['color'] ?? 'gray'] ?? 'is-gray'])
                    >
                        <div class="aura-myday-card-top">
                            <span class="aura-myday-card-icon" aria-hidden="true">
                                @include('filament.pages.partials.my-day-icon', ['icon' => $card['icon'] ?? 'chart'])
                            </span>
                            <span class="aura-myday-card-label">{{ $card['label'] }}</span>
                        </div>
                        <div class="aura-myday-card-value">{{ $card['value'] }}</div>
                        @if (filled($card['hint'] ?? null))
                            <div class="aura-myday-card-hint">{{ $card['hint'] }}</div>
                        @endif
                    </a>
                @endforeach
            </div>
        </section>

        @if (! empty($day['links']))
            <section class="aura-myday-section">
                <div class="aura-myday-section-head">
                    <h3>{{ $indonesian ? 'Pintasan cepat' : 'Quick links' }}</h3>
                    <p>{{ $indonesian ? 'Akses modul yang paling sering dipakai peran Anda.' : 'Jump to the modules your role uses most.' }}</p>
                </div>

                <div class="aura-myday-links">
                    @foreach ($day['links'] as $link)
                        <a
                            href="{{ $link['href'] }}"
                            @class(['aura-myday-link', $toneMap[$link['color'] ?? 'gray'] ?? 'is-gray'])
                        >
                            {{ $link['label'] }}
                            <span aria-hidden="true">↗</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-filament-panels::page>
