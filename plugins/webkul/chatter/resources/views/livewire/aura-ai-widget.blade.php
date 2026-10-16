<div class="aura-ai-assistant-root" @class(['is-open' => $isOpen])>
    <style>
        .aura-ai-assistant-root {
            --aura-ai-accent: var(--primary-600, #4f46e5);
            --aura-ai-accent-strong: var(--primary-700, #4338ca);
            position: fixed;
            right: 1.25rem;
            bottom: 1.5rem;
            z-index: 40;
            font-family: inherit;
            pointer-events: none;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.65rem;
        }

        .aura-ai-assistant-root > * {
            pointer-events: auto;
        }

        /* Compact FAB: leave room near pagination without a large dead zone */
        .fi-pagination,
        .fi-ta-pagination {
            padding-inline-end: 4.5rem !important;
        }

        @media (max-width: 640px) {
            .aura-ai-assistant-root {
                right: 0.85rem;
                bottom: 1.25rem;
            }

            .fi-pagination,
            .fi-ta-pagination {
                padding-inline-end: 0.75rem !important;
                padding-bottom: 4.25rem !important;
            }
        }

        .aura-ai-fab {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            min-width: 3.25rem;
            height: 3.25rem;
            padding: 0 0.85rem;
            border-radius: 9999px;
            border: 1px solid color-mix(in srgb, var(--aura-ai-accent) 35%, transparent);
            background: var(--aura-ai-accent);
            color: #ffffff;
            font-size: 0.8125rem;
            font-weight: 600;
            letter-spacing: 0.01em;
            box-shadow:
                0 1px 2px rgba(15, 23, 42, 0.08),
                0 8px 20px -8px color-mix(in srgb, var(--aura-ai-accent) 55%, transparent);
            cursor: pointer;
            transition:
                transform 0.18s ease,
                box-shadow 0.18s ease,
                gap 0.18s ease,
                background 0.18s ease;
        }

        .aura-ai-fab-icon {
            display: inline-flex;
            width: 1.15rem;
            height: 1.15rem;
            flex-shrink: 0;
        }

        .aura-ai-fab-icon svg {
            width: 100%;
            height: 100%;
        }

        .aura-ai-fab-label {
            max-width: 0;
            overflow: hidden;
            opacity: 0;
            white-space: nowrap;
            transition: max-width 0.2s ease, opacity 0.15s ease, margin 0.2s ease;
        }

        .aura-ai-fab:hover,
        .aura-ai-fab:focus-visible {
            gap: 0.45rem;
            transform: translateY(-1px);
            background: var(--aura-ai-accent-strong);
            box-shadow:
                0 2px 4px rgba(15, 23, 42, 0.1),
                0 12px 24px -10px color-mix(in srgb, var(--aura-ai-accent) 60%, transparent);
            outline: none;
        }

        .aura-ai-fab:hover .aura-ai-fab-label,
        .aura-ai-fab:focus-visible .aura-ai-fab-label {
            max-width: 5rem;
            opacity: 1;
            margin-inline-start: 0;
        }

        .aura-ai-assistant-root.is-open .aura-ai-fab {
            background: #475569;
            border-color: rgba(71, 85, 105, 0.4);
            box-shadow: 0 4px 12px -4px rgba(15, 23, 42, 0.35);
        }

        .aura-ai-assistant-root.is-open .aura-ai-fab:hover,
        .aura-ai-assistant-root.is-open .aura-ai-fab:focus-visible {
            background: #334155;
        }

        .aura-ai-panel {
            width: min(24rem, calc(100vw - 2rem));
            height: min(32rem, calc(100vh - 6rem));
            display: flex;
            flex-direction: column;
            border-radius: 1rem;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            box-shadow:
                0 4px 6px -2px rgba(15, 23, 42, 0.06),
                0 18px 36px -16px rgba(15, 23, 42, 0.28);
            animation: aura-ai-panel-in 0.18s ease-out;
        }

        @keyframes aura-ai-panel-in {
            from {
                opacity: 0;
                transform: translateY(0.5rem) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .dark .aura-ai-panel {
            background: #111827;
            border-color: #374151;
            color: #f9fafb;
        }

        .aura-ai-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            background: var(--aura-ai-accent);
            color: #ffffff;
        }

        .aura-ai-header-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .aura-ai-header-actions {
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .aura-ai-icon-btn {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border: none;
            border-radius: 0.375rem;
            padding: 0.25rem 0.45rem;
            font-size: 0.75rem;
            cursor: pointer;
        }

        .aura-ai-icon-btn:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .aura-ai-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
            padding: 0.55rem 0.75rem;
            border-bottom: 1px solid #f3f4f6;
            background: #f9fafb;
        }

        .dark .aura-ai-chips {
            background: #1f2937;
            border-color: #374151;
        }

        .aura-ai-chip {
            font-size: 0.72rem;
            padding: 0.2rem 0.55rem;
            border-radius: 9999px;
            border: 1px solid #d1d5db;
            background: #ffffff;
            color: #374151;
            cursor: pointer;
        }

        .dark .aura-ai-chip {
            background: #111827;
            border-color: #4b5563;
            color: #e5e7eb;
        }

        .aura-ai-chip:hover {
            border-color: var(--aura-ai-accent);
            color: var(--aura-ai-accent);
        }

        .aura-ai-messages {
            flex: 1 1 auto;
            overflow-y: auto;
            padding: 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .aura-ai-msg {
            max-width: 88%;
            padding: 0.55rem 0.75rem;
            border-radius: 0.75rem;
            font-size: 0.8125rem;
            line-height: 1.35;
            white-space: pre-line;
        }

        .aura-ai-msg-assistant {
            align-self: flex-start;
            background: #f3f4f6;
            color: #111827;
            border-bottom-left-radius: 0.2rem;
        }

        .dark .aura-ai-msg-assistant {
            background: #1f2937;
            color: #f3f4f6;
        }

        .aura-ai-msg-user {
            align-self: flex-end;
            background: var(--aura-ai-accent);
            color: #ffffff;
            border-bottom-right-radius: 0.2rem;
        }

        .aura-ai-time {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.68rem;
            opacity: 0.7;
        }

        .aura-ai-form {
            display: flex;
            gap: 0.4rem;
            padding: 0.65rem 0.75rem;
            border-top: 1px solid #e5e7eb;
            background: #ffffff;
        }

        .dark .aura-ai-form {
            background: #111827;
            border-color: #374151;
        }

        .aura-ai-input {
            flex: 1 1 auto;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 0.45rem 0.65rem;
            font-size: 0.8125rem;
            background: transparent;
            color: inherit;
        }

        .dark .aura-ai-input {
            border-color: #4b5563;
        }

        .aura-ai-send {
            background: var(--aura-ai-accent);
            color: #ffffff;
            border: none;
            border-radius: 0.5rem;
            padding: 0.45rem 0.75rem;
            font-size: 0.8125rem;
            font-weight: 600;
            cursor: pointer;
        }
    </style>

    @if ($isOpen)
        <div class="aura-ai-panel" role="dialog" aria-label="{{ __('chatter::ai.widget.title') }}">
            <div class="aura-ai-header">
                <div class="aura-ai-header-title">
                    <span class="aura-ai-fab-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"/>
                        </svg>
                    </span>
                    <span>{{ __('chatter::ai.widget.title') }}</span>
                </div>
                <div class="aura-ai-header-actions">
                    <button type="button" class="aura-ai-icon-btn" wire:click="clearChat">
                        {{ __('chatter::ai.widget.reset') }}
                    </button>
                    <button type="button" class="aura-ai-icon-btn" wire:click="toggle">
                        ✕
                    </button>
                </div>
            </div>

            <div class="aura-ai-chips">
                <button type="button" class="aura-ai-chip" wire:click="ask('{{ __('chatter::ai.widget.presets.attendance') }}')">
                    {{ __('chatter::ai.widget.presets.attendance') }}
                </button>
                <button type="button" class="aura-ai-chip" wire:click="ask('{{ __('chatter::ai.widget.presets.contracts') }}')">
                    {{ __('chatter::ai.widget.presets.contracts') }}
                </button>
                <button type="button" class="aura-ai-chip" wire:click="ask('{{ __('chatter::ai.widget.presets.performance') }}')">
                    {{ __('chatter::ai.widget.presets.performance') }}
                </button>
                <button type="button" class="aura-ai-chip" wire:click="ask('{{ __('chatter::ai.widget.presets.employees') }}')">
                    {{ __('chatter::ai.widget.presets.employees') }}
                </button>
                <button type="button" class="aura-ai-chip" wire:click="ask('{{ __('chatter::ai.widget.presets.assets') }}')">
                    {{ __('chatter::ai.widget.presets.assets') }}
                </button>
            </div>

            <div class="aura-ai-messages">
                @foreach ($messages as $item)
                    <div class="aura-ai-msg {{ $item['role'] === 'user' ? 'aura-ai-msg-user' : 'aura-ai-msg-assistant' }}">
                        {{ $item['text'] }}
                        <span class="aura-ai-time">{{ $item['time'] }}</span>
                    </div>
                @endforeach
            </div>

            <form class="aura-ai-form" wire:submit="ask">
                <input
                    type="text"
                    class="aura-ai-input"
                    wire:model="question"
                    placeholder="{{ __('chatter::ai.widget.placeholder') }}"
                />
                <button type="submit" class="aura-ai-send">
                    {{ __('chatter::ai.widget.send') }}
                </button>
            </form>
        </div>
    @endif

    <button
        type="button"
        class="aura-ai-fab"
        wire:click="toggle"
        title="{{ __('chatter::ai.widget.button') }}"
        aria-label="{{ __('chatter::ai.widget.button') }}"
        aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
    >
        <span class="aura-ai-fab-icon" aria-hidden="true">
            @if ($isOpen)
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            @else
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"/>
                </svg>
            @endif
        </span>
        @unless ($isOpen)
            <span class="aura-ai-fab-label">{{ __('chatter::ai.widget.button') }}</span>
        @endunless
    </button>
</div>
