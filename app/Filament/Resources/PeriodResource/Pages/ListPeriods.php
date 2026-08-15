<?php
// app/Filament/Resources/PeriodResource/Pages/ListPeriods.php

namespace App\Filament\Resources\PeriodResource\Pages;

use App\Filament\Resources\PeriodResource;
use Filament\Resources\Pages\ListRecords;

class ListPeriods extends ListRecords
{
    protected static string $resource = PeriodResource::class;

    // No "Create" button — rows are seeded by the migration.
    // New period types should be added via a new migration seed.
    protected function getHeaderActions(): array
    {
        return [];
    }
}