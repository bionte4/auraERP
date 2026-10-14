<?php

namespace Webkul\Chatter\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Webkul\Chatter\Services\AuraAiAssistantService;

class AuraAiWidget extends Component
{
    public bool $isOpen = false;

    public string $question = '';

    /**
     * @var array<int, array{role: string, text: string, time: string}>
     */
    public array $messages = [];

    public function mount(): void
    {
        $this->messages = [
            [
                'role' => 'assistant',
                'text' => __('chatter::ai.widget.greeting'),
                'time' => now()->format('H:i'),
            ],
        ];
    }

    public function toggle(): void
    {
        $this->isOpen = ! $this->isOpen;
    }

    public function ask(?string $preset = null): void
    {
        $text = trim($preset !== null ? $preset : $this->question);

        if ($text === '') {
            return;
        }

        $this->messages[] = [
            'role' => 'user',
            'text' => $text,
            'time' => now()->format('H:i'),
        ];

        $this->question = '';

        $reply = app(AuraAiAssistantService::class)->answer($text);

        $this->messages[] = [
            'role' => 'assistant',
            'text' => $reply,
            'time' => now()->format('H:i'),
        ];
    }

    public function clearChat(): void
    {
        $this->mount();
    }

    public function render(): View
    {
        return view('chatter::livewire.aura-ai-widget');
    }
}
