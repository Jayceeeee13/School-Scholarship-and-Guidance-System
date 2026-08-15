<?php

namespace App\Filament\Resources\AnecdotalsResource\Pages;

use App\Filament\Resources\AnecdotalsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAnecdotals extends EditRecord
{
    protected static string $resource = AnecdotalsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
