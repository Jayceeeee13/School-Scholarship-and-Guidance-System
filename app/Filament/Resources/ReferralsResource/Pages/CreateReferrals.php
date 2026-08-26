<?php

namespace App\Filament\Resources\ReferralsResource\Pages;

use App\Filament\Resources\ReferralsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReferrals extends CreateRecord
{
    protected static string $resource = ReferralsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // ── Endorsement ───────────────────────────────────────────
        if (empty($data['has_endorsement'])) {
            unset($data['endorsement']);
        }
        unset($data['has_endorsement']);

        // ── Invitation ────────────────────────────────────────────
        if (empty($data['has_invitation'])) {
            unset($data['invitation']);
        }
        unset($data['has_invitation']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}