<?php
// app/Filament/Resources/PeriodResource/Pages/EditPeriod.php

namespace App\Filament\Resources\PeriodResource\Pages;

use App\Filament\Resources\PeriodResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPeriod extends EditRecord
{
    protected static string $resource = PeriodResource::class;

    // No delete action — period rows are permanent
    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Period updated')
            ->body('The period settings have been saved successfully.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}