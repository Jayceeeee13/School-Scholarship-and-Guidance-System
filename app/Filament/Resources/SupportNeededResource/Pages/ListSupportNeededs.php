<?php

namespace App\Filament\Resources\SupportNeededResource\Pages;

use App\Filament\Resources\SupportNeededResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSupportNeededs extends ListRecords
{
    protected static string $resource = SupportNeededResource::class;

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
