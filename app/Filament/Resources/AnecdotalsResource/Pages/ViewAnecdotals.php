<?php

namespace App\Filament\Resources\AnecdotalsResource\Pages;

use App\Filament\Resources\AnecdotalsResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewAnecdotals extends ViewRecord
{
    protected static string $resource = AnecdotalsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}