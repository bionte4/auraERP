<div class="aura-tracks">
    <style>
        .aura-tracks {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            max-height: 24rem;
            overflow: auto;
        }

        .aura-tracks a,
        .aura-tracks p {
            margin: 0;
            font-size: 0.9rem;
        }

        .aura-tracks a {
            color: inherit;
            font-weight: 650;
        }
    </style>

    @forelse ($tracks as $track)
        @if ($track->mapUrl())
            <a href="{{ $track->mapUrl() }}" target="_blank" rel="noopener noreferrer">
                {{ $track->recorded_at?->format('H:i:s') }}
                —
                {{ number_format((float) $track->latitude, 5) }}, {{ number_format((float) $track->longitude, 5) }}
            </a>
        @endif
    @empty
        <p>{{ __('attendances::filament/resources/attendance.tracks.empty') }}</p>
    @endforelse
</div>
