<?php

namespace App\Filament\Resources\SchoolPositionResource\Pages;

use App\Filament\Resources\SchoolPositionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSchoolPositions extends ListRecords
{
    protected static string $resource = SchoolPositionResource::class;

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
