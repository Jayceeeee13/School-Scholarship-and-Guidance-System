<?php

namespace App\Filament\Resources\RequirementResource\Pages;

use App\Filament\Resources\RequirementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRequirements extends ListRecords
{
    protected static string $resource = RequirementResource::class;

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
