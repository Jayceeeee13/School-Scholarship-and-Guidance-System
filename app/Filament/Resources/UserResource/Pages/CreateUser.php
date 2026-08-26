<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use App\Models\Personnels;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! empty($data['personnel_id'])) {
            $personnel = Personnels::find($data['personnel_id']);

            if ($personnel) {
                $data['name']       = "{$personnel->first_name} {$personnel->last_name}";
                $data['email']      = $personnel->email;
                $data['contact_no'] = $personnel->contact_no;
                $data['birthdate']  = $personnel->birthdate;
                $data['address']    = $personnel->address;
                $data['gender_id']  = $personnel->gender_id;
            }
        }

        $data['password'] = bcrypt($data['password'] ?? 'GVCFI@2026');

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}