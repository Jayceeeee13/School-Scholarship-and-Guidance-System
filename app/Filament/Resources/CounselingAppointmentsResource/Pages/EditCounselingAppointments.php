<?php

namespace App\Filament\Resources\CounselingAppointmentsResource\Pages;

use App\Filament\Resources\CounselingAppointmentsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCounselingAppointments extends EditRecord
{
    protected static string $resource = CounselingAppointmentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['has_endorsement'])) {
            // Delete the existing endorsement record if toggle is turned off
            $this->record->endorsement()?->delete();
            unset($data['endorsement']);
        }

        // has_endorsement is not a real DB column, remove it before saving
        unset($data['has_endorsement']);

        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        // Always overwrite with fresh data from the appointment
        $data['endorsement']['name'] = trim(
            "{$record->first_name} " .
            "{$record->middle_name} " .
            "{$record->last_name}"
        );

        $data['endorsement']['course_and_year'] = $record->course_and_year;

        return $data;
    }
}