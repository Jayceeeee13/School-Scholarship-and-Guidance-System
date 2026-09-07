<?php

namespace App\Filament\Resources\CounselingAppointmentsResource\Pages;

use App\Filament\Resources\CounselingAppointmentsResource;
use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewCounselingAppointments extends ViewRecord
{
    protected static string $resource = CounselingAppointmentsResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return CounselingAppointmentsResource::infolist($infolist);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->url(fn () => CounselingAppointmentsResource::getUrl('edit', [
                    'record' => $this->record->id,
                    'tab'    => '-personal-information-tab',
                ])),
            Actions\DeleteAction::make(),
        ];
    }
}