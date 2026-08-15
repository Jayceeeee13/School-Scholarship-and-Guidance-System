<?php

namespace App\Filament\Resources\TypeOfApplicationResource\Pages;

use App\Filament\Resources\TypeOfApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTypeOfApplications extends ListRecords
{
    protected static string $resource = TypeOfApplicationResource::class;

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
