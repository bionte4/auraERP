<div class="aura-ai-assistant-root">
    <style>
        .aura-ai-assistant-root {
            position: fixed;
            right: 1.25rem;
            bottom: 1.25rem;
            z-index: 45;
            font-family: inherit;
        }

        .aura-ai-fab {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1rem;
            border-radius: 9999px;
            border: 1px solid rgba(59, 130, 246, 0.35);
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            font-size: 0.85rem;
            font-weight: 600;
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.5);
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .aura-ai-fab:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 28px -5px rgba(37, 99, 235, 0.65);
        }

        .aura-ai-panel {
            width: min(24rem, calc(100vw - 2rem));
            height: min(32rem, calc(100vh - 6rem));
            margin-bottom: 0.75rem;
            display: flex;
            flex-direction: column;
            border-radius: 0.9rem;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.25);
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
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
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
            border-color: #2563eb;
            color: #2563eb;
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
            background: #2563eb;
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
            background: #2563eb;
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
        <div class="aura-ai-panel">
            <div class="aura-ai-header">
                <div class="aura-ai-header-title">
                    <span>✨</span>
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

    <button type="button" class="aura-ai-fab" wire:click="toggle">
        <span>✨</span>
        <span>{{ __('chatter::ai.widget.button') }}</span>
    </button>
</div>
