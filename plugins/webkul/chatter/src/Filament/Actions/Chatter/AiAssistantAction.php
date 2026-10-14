<?php

namespace Webkul\Chatter\Filament\Actions\Chatter;

use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Throwable;
use Webkul\Chatter\Services\AuraAiAssistantService;

class AiAssistantAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'ai.assistant.action';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->color('primary')
            ->outlined()
            ->label(__('chatter::ai.chatter.button'))
            ->icon('heroicon-o-sparkles')
            ->modalIcon('heroicon-o-sparkles')
            ->modalHeading(__('chatter::ai.chatter.modal-heading'))
            ->modalDescription(__('chatter::ai.chatter.modal-description'))
            ->schema([
                Textarea::make('prompt')
                    ->label(__('chatter::ai.chatter.prompt-label'))
                    ->placeholder(__('chatter::ai.chatter.prompt-placeholder'))
                    ->rows(3),
            ])
            ->action(function (array $data, ?Model $record = null): void {
                if (! $record) {
                    return;
                }

                try {
                    $prompt = (string) ($data['prompt'] ?? '');

                    app(AuraAiAssistantService::class)->replyOnRecord($record, $prompt);

                    Notification::make()
                        ->success()
                        ->title(__('chatter::ai.chatter.notification-title'))
                        ->body(__('chatter::ai.chatter.notification-body'))
                        ->send();
                } catch (Throwable $e) {
                    report($e);
                }
            })
            ->after(function ($livewire): void {
                if (method_exists($livewire, 'dispatch')) {
                    $livewire->dispatch('chatter.refresh');
                }
            })
            ->modalSubmitActionLabel(__('chatter::ai.chatter.submit'))
            ->slideOver(false);
    }
}
