<div class="aura-clock">
    <style>
        [x-cloak] { display: none !important; }

        .aura-clock {
            max-width: 28rem;
            margin-inline: auto;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .aura-clock-card {
            border: 1px solid color-mix(in srgb, currentColor 14%, transparent);
            border-radius: 1.25rem;
            padding: 1.25rem;
            text-align: center;
        }

        .aura-clock-kicker {
            margin: 0;
            font-size: 0.85rem;
            opacity: 0.7;
        }

        .aura-clock-time {
            margin: 0.25rem 0 0;
            font-size: 2.75rem;
            font-weight: 650;
            letter-spacing: -0.04em;
            font-variant-numeric: tabular-nums;
        }

        .aura-clock-name {
            margin: 0.75rem 0 0;
            font-size: 1.15rem;
            font-weight: 650;
        }

        .aura-clock-status {
            margin: 0.35rem 0 0;
            font-size: 0.95rem;
        }

        .aura-clock-btn {
            width: 100%;
            min-height: 4.75rem;
            border: 0;
            border-radius: 1rem;
            font-size: 1.2rem;
            font-weight: 650;
            cursor: pointer;
        }

        .aura-clock-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .aura-clock-btn-in {
            background: #15803d;
            color: #fff;
        }

        .aura-clock-btn-out {
            background: #18181b;
            color: #fff;
        }

        .aura-clock-btn-enroll {
            background: #1d4ed8;
            color: #fff;
        }

        .aura-clock-btn-reenroll {
            margin-top: 0.75rem;
            min-height: 3rem;
            background: transparent;
            color: inherit;
            border: 1px solid color-mix(in srgb, currentColor 22%, transparent);
            font-size: 0.95rem;
        }

        .aura-clock-video {
            width: 100%;
            max-height: 22rem;
            aspect-ratio: 3 / 4;
            object-fit: cover;
            border-radius: 1rem;
            background: #111;
            transform: scaleX(-1);
        }

        .aura-clock-note,
        .aura-clock-links {
            margin: 0;
            font-size: 0.85rem;
            line-height: 1.45;
            opacity: 0.75;
            text-align: center;
        }

        .aura-clock-links {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .aura-clock-links a {
            color: inherit;
            font-weight: 650;
        }
    </style>

    <section class="aura-clock-card">
        <p class="aura-clock-kicker">{{ now()->locale(app()->getLocale())->translatedFormat('l, j F Y') }}</p>
        <p
            class="aura-clock-time"
            x-data="{ time: '' }"
            x-init="
                const tick = () => { time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' }) };
                tick();
                setInterval(tick, 1000);
            "
            x-text="time"
        ></p>
        @if ($employee)
            <p class="aura-clock-name">{{ $employee->name }}</p>
            <p class="aura-clock-status">
                @if ($attendance?->check_out)
                    {{ __('attendances::filament/pages/clock.status.complete', ['in' => $attendance->check_in?->format('H:i'), 'out' => $attendance->check_out?->format('H:i')]) }}
                @elseif ($attendance?->check_in)
                    {{ __('attendances::filament/pages/clock.status.open', ['in' => $attendance->check_in?->format('H:i'), 'status' => $attendance->status?->getLabel()]) }}
                @else
                    {{ __('attendances::filament/pages/clock.status.none') }}
                @endif
            </p>
        @else
            <p class="aura-clock-name">{{ __('attendances::filament/pages/clock.missing-employee.title') }}</p>
            <p class="aura-clock-status">{{ __('attendances::filament/pages/clock.missing-employee.body') }}</p>
        @endif
    </section>

    @if ($employee)
        <div
            id="aura-clock-app"
            data-script="{{ $faceScript }}"
            data-model="{{ $faceModel }}"
            data-helper="{{ $faceHelper }}"
            data-loading="{{ __('attendances::filament/pages/clock.face.loading') }}"
            data-scanning="{{ __('attendances::filament/pages/clock.face.scanning') }}"
            data-locating="{{ __('attendances::filament/pages/clock.actions.locating') }}"
            data-ready="{{ $faceEnrolled ? __('attendances::filament/pages/clock.face.ready') : __('attendances::filament/pages/clock.face.needs-enroll') }}"
            data-tracking="{{ $tracking ? '1' : '0' }}"
            data-tracking-label="{{ __('attendances::filament/pages/clock.tracking.active') }}"
            x-data
            x-init="
                const boot = () => { if (window.auraClockFace) window.auraClockFace.preview($wire) };
                if (window.auraClockFace) { boot(); return; }
                const script = document.createElement('script');
                script.src = $el.dataset.helper;
                script.onload = boot;
                document.head.appendChild(script);
            "
        >
            <video id="aura-clock-video" class="aura-clock-video" autoplay muted playsinline></video>
            <p id="aura-clock-face-status" class="aura-clock-status"></p>

            @if (! $faceEnrolled && $canClockIn)
                <button
                    type="button"
                    class="aura-clock-btn aura-clock-btn-enroll"
                    x-on:click="window.auraClockFace && window.auraClockFace.enroll($wire)"
                >
                    {{ __('attendances::filament/pages/clock.actions.enroll') }}
                </button>
            @endif

            @if ($faceEnrolled && $canClockIn)
                <button
                    type="button"
                    class="aura-clock-btn aura-clock-btn-in"
                    @disabled($attendance?->check_in)
                    x-on:click="window.auraClockFace && window.auraClockFace.clock($wire, 'clockIn')"
                >
                    {{ __('attendances::filament/pages/clock.actions.clock-in') }}
                </button>
            @endif

            @if ($faceEnrolled && $canClockOut)
                <button
                    type="button"
                    class="aura-clock-btn aura-clock-btn-out"
                    style="margin-top: 0.75rem;"
                    @disabled(! $attendance?->check_in || $attendance?->check_out)
                    x-on:click="window.auraClockFace && window.auraClockFace.clock($wire, 'clockOut')"
                >
                    {{ __('attendances::filament/pages/clock.actions.clock-out') }}
                </button>
            @endif

            @if ($faceEnrolled && $canClockIn)
                <button
                    type="button"
                    class="aura-clock-btn aura-clock-btn-reenroll"
                    x-on:click="window.auraClockFace && window.auraClockFace.enroll($wire)"
                >
                    {{ __('attendances::filament/pages/clock.actions.reenroll') }}
                </button>
            @endif
        </div>

        @if ($tracking)
            <p class="aura-clock-note">
                {{ __('attendances::filament/pages/clock.tracking.active') }}
                <span id="aura-clock-track-count">{{ $trackCount }}</span>
            </p>
        @endif

        <p class="aura-clock-note">{{ __('attendances::filament/pages/clock.tracking.note') }}</p>
        <p class="aura-clock-note">{{ __('attendances::filament/pages/clock.face-note') }}</p>

        <div class="aura-clock-links">
            @if ($attendance?->checkInMapUrl())
                <a href="{{ $attendance->checkInMapUrl() }}" target="_blank" rel="noopener noreferrer">
                    {{ __('attendances::filament/pages/clock.links.check-in', ['place' => number_format((float) $attendance->check_in_latitude, 5).', '.number_format((float) $attendance->check_in_longitude, 5)]) }}
                </a>
            @endif
            @if ($attendance?->checkOutMapUrl())
                <a href="{{ $attendance->checkOutMapUrl() }}" target="_blank" rel="noopener noreferrer">
                    {{ __('attendances::filament/pages/clock.links.check-out', ['place' => number_format((float) $attendance->check_out_latitude, 5).', '.number_format((float) $attendance->check_out_longitude, 5)]) }}
                </a>
            @endif
        </div>
    @endif
</div>
