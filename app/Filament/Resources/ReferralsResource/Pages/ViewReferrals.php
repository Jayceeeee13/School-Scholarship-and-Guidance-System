<?php

namespace App\Filament\Resources\ReferralsResource\Pages;

use App\Filament\Resources\ReferralsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewReferrals extends ViewRecord
{
    protected static string $resource = ReferralsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('add_logform')
                ->label('Add Logform')
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->url(fn () => route('filament.admin.resources.counseling-logforms.create', [
                    'referral_id' => $this->record->id,
                ]))
                ->visible(fn () => auth()->user()->hasAnyRole(['admin', 'guidance'])),
            
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil')
                ->color('warning'),
            
            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->visible(fn () => auth()->user()->hasRole('admin')),
        ];
    }
}