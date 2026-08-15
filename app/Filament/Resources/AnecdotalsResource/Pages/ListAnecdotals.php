<?php

namespace App\Filament\Resources\AnecdotalsResource\Pages;

use App\Filament\Resources\AnecdotalsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAnecdotals extends ListRecords
{
    protected static string $resource = AnecdotalsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
