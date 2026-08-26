<?php

namespace App\Filament\Resources\CounselingAppointmentsResource\Pages;

use App\Filament\Resources\CounselingAppointmentsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCounselingAppointments extends CreateRecord
{
    protected static string $resource = CounselingAppointmentsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['has_endorsement'])) {
            // Don't save endorsement data if toggle is off
            unset($data['endorsement']);
        }

        // has_endorsement is not a real DB column, remove it before saving
        unset($data['has_endorsement']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}