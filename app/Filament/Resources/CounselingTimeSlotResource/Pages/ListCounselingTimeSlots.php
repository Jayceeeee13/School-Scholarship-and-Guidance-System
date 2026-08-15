<?php

namespace App\Filament\Resources\CounselingTimeSlotResource\Pages;

use App\Filament\Resources\CounselingTimeSlotResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCounselingTimeSlots extends ListRecords
{
    protected static string $resource = CounselingTimeSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back to Settings')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(route('filament.admin.pages.manage-settings')),
            Actions\CreateAction::make(),
        ];
    }
}
