<?php

namespace Webkul\Security\Filament\Resources\UserResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Filament\Resources\UserResource;
use Webkul\Security\Models\User;
use Webkul\Security\Services\SecurityAuditLogger;
use Webkul\Security\Settings\UserSettings;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    /**
     * @var array<int, string>
     */
    protected array $rolesBeforeSave = [];

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('security::filament/resources/user/pages/edit-user.notification.title'))
            ->body(__('security::filament/resources/user/pages/edit-user.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('changePassword')
                ->label(__('security::filament/resources/user/pages/edit-user.header-actions.change-password.label'))
                ->visible(fn (UserSettings $userSettings) => $userSettings->enable_reset_password)
                ->action(function (User $record, array $data): void {
                    $record->update([
                        'password' => Hash::make($data['new_password']),
                    ]);

                    Notification::make()
                        ->title(__('security::filament/resources/user/pages/edit-user.header-actions.change-password.notification.title'))
                        ->body(__('security::filament/resources/user/pages/edit-user.header-actions.change-password.notification.body'))
                        ->success()
                        ->send();
                })
                ->schema([
                    TextInput::make('new_password')
                        ->password()
                        ->revealable()
                        ->label(__('security::filament/resources/user/pages/edit-user.header-actions.change-password.form.new-password'))
                        ->required()
                        ->rule(Password::default()),
                    TextInput::make('new_password_confirmation')
                        ->password()
                        ->revealable()
                        ->label(__('security::filament/resources/user/pages/edit-user.header-actions.change-password.form.confirm-new-password'))
                        ->rule('required', fn ($get) => (bool) $get('new_password'))
                        ->same('new_password'),
                ])
                ->icon('heroicon-o-key'),
            ViewAction::make(),
            DeleteAction::make()
                ->before(function (DeleteAction $action, User $record): void {
                    if (! self::getResource()::canDeleteUser($record)) {
                        Notification::make()
                            ->danger()
                            ->title(__('security::filament/resources/user/pages/edit-user.header-actions.delete.notification.error.title'))
                            ->body(__('security::filament/resources/user/pages/edit-user.header-actions.delete.notification.error.body'))
                            ->send();

                        $action->cancel();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('security::filament/resources/user/pages/edit-user.header-actions.delete.notification.title'))
                        ->body(__('security::filament/resources/user/pages/edit-user.header-actions.delete.notification.body'))
                ),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $partner = $this->record?->partner;

        if (! $partner) {
            return $data;
        }

        return [
            ...$data,
            ...$partner->toArray(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! (Auth::id() === $this->record->id && array_key_exists('resource_permission', $data))) {
            return $data;
        }

        $submittedPermission = $data['resource_permission'];

        if ($submittedPermission instanceof PermissionType) {
            $submittedPermission = $submittedPermission->value;
        }

        $currentPermission = $this->record->resource_permission?->value;

        if ((string) $submittedPermission !== (string) $currentPermission) {
            throw ValidationException::withMessages([
                'resource_permission' => __('security::filament/resources/user.form.sections.permissions.fields.resource-permission-self-change-disabled'),
            ]);
        }

        return $data;
    }

    protected function beforeSave(): void
    {
        if ($this->record instanceof User) {
            $this->rolesBeforeSave = $this->record->roles()
                ->pluck('name')
                ->map(fn ($name): string => (string) $name)
                ->sort()
                ->values()
                ->all();
        }

        $rawState = $this->form->getRawState();

        if (! array_key_exists('roles', $rawState) && ! array_key_exists('roles', $this->data ?? [])) {
            return;
        }

        $roleIds = $rawState['roles'] ?? $this->data['roles'] ?? $this->record->roles()->pluck('id')->all();

        try {
            UserResource::ensureAdminRoleConstraints($this->record, (array) $roleIds);
        } catch (ValidationException $exception) {
            Notification::make()
                ->danger()
                ->title(__('security::filament/resources/user.form.sections.permissions.fields.roles'))
                ->body(implode("\n", $exception->errors()['roles'] ?? []))
                ->send();

            $this->halt();
        }
    }

    protected function afterSave(): void
    {
        $record = $this->record;

        if (! $record instanceof User) {
            return;
        }

        $rolesAfter = $record->roles()
            ->pluck('name')
            ->map(fn ($name): string => (string) $name)
            ->sort()
            ->values()
            ->all();

        if ($rolesAfter === $this->rolesBeforeSave) {
            return;
        }

        app(SecurityAuditLogger::class)->logUserRolesChanged($record, $rolesAfter);
    }
}
