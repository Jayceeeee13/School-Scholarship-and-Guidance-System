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
     * User → Personnel sync (the reverse of ListUsers::syncPersonnelToUser).
     *
     * Name is intentionally NOT synced back: a single `name` can't be
     * reliably split into first / middle / last name.
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