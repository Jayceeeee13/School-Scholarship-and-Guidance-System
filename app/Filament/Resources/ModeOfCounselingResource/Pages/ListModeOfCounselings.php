<?php

namespace App\Filament\Resources\ModeOfCounselingResource\Pages;

use App\Filament\Resources\ModeOfCounselingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListModeOfCounselings extends ListRecords
{
    protected static string $resource = ModeOfCounselingResource::class;

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
