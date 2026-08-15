<?php

namespace App\Filament\Resources\GenderResource\Pages;

use App\Filament\Resources\GenderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGenders extends ListRecords
{
    protected static string $resource = GenderResource::class;

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
