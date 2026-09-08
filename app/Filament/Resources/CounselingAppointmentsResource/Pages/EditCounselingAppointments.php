<?php

namespace App\Filament\Resources\CounselingAppointmentsResource\Pages;

use App\Filament\Resources\CounselingAppointmentsResource;
use App\Models\CounselingAppointments;
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
        /*
        |--------------------------------------------------------------------------
        | Endorsement
        |--------------------------------------------------------------------------
        */

        if (empty($data['has_endorsement'])) {

            // Delete existing endorsement when toggle is disabled
            $this->record->endorsement()?->delete();

            unset($data['endorsement']);
        }

        // has_endorsement is not an actual database column
        unset($data['has_endorsement']);

        /*
        |--------------------------------------------------------------------------
        | Completed Appointment Protection
        |--------------------------------------------------------------------------
        |
        | Once the counseling session is completed, don't allow an edit
        | to accidentally change it back to pending/approved.
        |
        */

        if ($this->record->status === 'completed') {
            $data['status'] = 'completed';
        }

        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        /*
        |--------------------------------------------------------------------------
        | Endorsement Information
        |--------------------------------------------------------------------------
        */

        $data['endorsement']['name'] = trim(
            "{$record->first_name} " .
            "{$record->middle_name} " .
            "{$record->last_name}"
        );

        $data['endorsement']['course_and_year'] =
            $record->course_and_year;

        return $data;
    }
}