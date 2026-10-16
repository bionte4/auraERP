<?php

namespace Webkul\Security\Filament\Resources\AuditTrailResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Security\Enums\SecurityAuditEvent;

class AuditTrailInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('security::filament/resources/audit-trail.infolist.sections.overview'))
                ->schema([
                    TextEntry::make('created_at')
                        ->label(__('security::filament/resources/audit-trail.table.columns.occurred-at'))
                        ->dateTime(),
                    TextEntry::make('event')
                        ->label(__('security::filament/resources/audit-trail.table.columns.event'))
                        ->badge()
                        ->formatStateUsing(fn ($state): string => $state instanceof SecurityAuditEvent
                            ? $state->getLabel()
                            : (string) $state)
                        ->color(fn ($state): string|array|null => $state instanceof SecurityAuditEvent
                            ? $state->getColor()
                            : 'gray'),
                    TextEntry::make('description')
                        ->label(__('security::filament/resources/audit-trail.table.columns.description'))
                        ->columnSpanFull(),
                    TextEntry::make('actor_label')
                        ->label(__('security::filament/resources/audit-trail.table.columns.actor'))
                        ->placeholder('—'),
                    TextEntry::make('subject_label')
                        ->label(__('security::filament/resources/audit-trail.table.columns.subject'))
                        ->placeholder('—'),
                    TextEntry::make('ip_address')
                        ->label(__('security::filament/resources/audit-trail.table.columns.ip'))
                        ->placeholder('—'),
                    TextEntry::make('guard')
                        ->label(__('security::filament/resources/audit-trail.table.columns.guard'))
                        ->placeholder('—'),
                    TextEntry::make('user_agent')
                        ->label(__('security::filament/resources/audit-trail.table.columns.user-agent'))
                        ->placeholder('—')
                        ->columnSpanFull(),
                    TextEntry::make('properties')
                        ->label(__('security::filament/resources/audit-trail.table.columns.properties'))
                        ->formatStateUsing(function ($state): string {
                            if (! is_array($state) || $state === []) {
                                return '—';
                            }

                            return collect($state)
                                ->map(fn ($value, $key): string => $key.': '.(is_scalar($value) ? (string) $value : json_encode($value)))
                                ->implode("\n");
                        })
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
