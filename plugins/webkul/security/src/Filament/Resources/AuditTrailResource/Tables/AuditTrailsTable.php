<?php

namespace Webkul\Security\Filament\Resources\AuditTrailResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Webkul\Security\Enums\SecurityAuditEvent;

class AuditTrailsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('security::filament/resources/audit-trail.table.columns.occurred-at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('event')
                    ->label(__('security::filament/resources/audit-trail.table.columns.event'))
                    ->badge()
                    ->formatStateUsing(fn ($state): string => $state instanceof SecurityAuditEvent
                        ? $state->getLabel()
                        : (string) $state)
                    ->color(fn ($state): string|array|null => $state instanceof SecurityAuditEvent
                        ? $state->getColor()
                        : 'gray')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('actor_label')
                    ->label(__('security::filament/resources/audit-trail.table.columns.actor'))
                    ->placeholder('—')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('subject_label')
                    ->label(__('security::filament/resources/audit-trail.table.columns.subject'))
                    ->placeholder('—')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('description')
                    ->label(__('security::filament/resources/audit-trail.table.columns.description'))
                    ->wrap()
                    ->limit(80)
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label(__('security::filament/resources/audit-trail.table.columns.ip'))
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('guard')
                    ->label(__('security::filament/resources/audit-trail.table.columns.guard'))
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->label(__('security::filament/resources/audit-trail.table.filters.event'))
                    ->options(collect(SecurityAuditEvent::cases())
                        ->mapWithKeys(fn (SecurityAuditEvent $event): array => [
                            $event->value => $event->getLabel(),
                        ])
                        ->all()),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->paginated([25, 50, 100]);
    }
}
