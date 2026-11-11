<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Traits\LogsCustomActivity;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    use LogsCustomActivity;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return UserResource::getUrl('view', ['record' => $this->record]);
    }

    /**
     * If nothing was changed, skip the save entirely:
     * no "Saved" notification, no personnel sync, no activity log.
     */
    protected function beforeSave(): void
    {
        if ($this->formHasChanges()) {
            return;
        }

        $this->redirect($this->getRedirectUrl(), navigate: true);

        $this->halt();
    }

    protected function formHasChanges(): bool
    {
        $record = $this->getRecord();

        // Dehydrated values (what would actually be written to the users table).
        $data = $this->form->getState(shouldCallHooksBefore: false);

        // Regular columns: fill a copy and see if anything is dirty.
        $copy = $record->replicate();
        $copy->setRawAttributes($record->getAttributes(), true);
        $copy->exists = true;
        $copy->fill($data);

        if ($copy->isDirty()) {
            return true;
        }

        // Roles are a relationship, so they are not part of $data.
        $newRoles = collect($this->data['roles'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values()
            ->all();

        $oldRoles = $record->roles()
            ->pluck('roles.id')
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values()
            ->all();

        return $newRoles !== $oldRoles;
    }

    /**
     * User → Personnel sync.
     *
     * user field   →  personnel field
     *   email      →  email
     *   contact_no →  contact_no
     *   birthdate  →  birthdate (+ age recalculated)
     *   address    →  address
     *   avatar     →  profile
     */
    protected function afterSave(): void
    {
        $user = $this->record->fresh();

        $personnel = $user->personnel;

        if (! $personnel) {
            return;
        }

        $personnel->update([
            'email'      => $user->email,
            'contact_no' => $user->contact_no,
            'birthdate'  => $user->birthdate,
            'age'        => $user->birthdate ? Carbon::parse($user->birthdate)->age : $personnel->age,
            'address'    => $user->address,
            'profile'    => $user->avatar,
        ]);

        $this->logCustomActivity(
            $personnel,
            'personnels',
            'updated',
            "Updated personnel {$personnel->first_name} {$personnel->last_name} (synced from user account)"
        );
    }
}