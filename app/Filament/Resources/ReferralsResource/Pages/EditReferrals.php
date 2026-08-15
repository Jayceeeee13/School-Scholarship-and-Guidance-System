<?php

namespace App\Filament\Resources\ReferralsResource\Pages;

use App\Filament\Resources\ReferralsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReferrals extends EditRecord
{
    protected static string $resource = ReferralsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()->hasRole('admin')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // ── Endorsement ───────────────────────────────────────────
        if (empty($data['has_endorsement'])) {
            $this->record->endorsement()?->delete();
            unset($data['endorsement']);
        }
        unset($data['has_endorsement']);
 
        // ── Invitation ────────────────────────────────────────────
        if (empty($data['has_invitation'])) {
            $this->record->invitation()?->delete();
            unset($data['invitation']);
        }
        unset($data['has_invitation']);
 
        return $data;
    }
 
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
 
        // Populate endorsement display fields with fresh referral data
        $data['endorsement']['name']            = $record->name;
        $data['endorsement']['course_and_year'] = $record->course_and_year;
 
        return $data;
    }
}