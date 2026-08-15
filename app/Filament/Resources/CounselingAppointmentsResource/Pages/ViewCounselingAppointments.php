<?php

namespace App\Filament\Resources\CounselingAppointmentsResource\Pages;

use App\Filament\Resources\CounselingAppointmentsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCounselingAppointments extends ViewRecord
{
    protected static string $resource = CounselingAppointmentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
