<?php

namespace App\Filament\Resources\ReferralsResource\Pages;

use App\Filament\Resources\ReferralsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReferrals extends EditRecord
{
    protected static string $resource = ReferralsResource::class;

    /**
     * Hash of the form state right after it was filled. Must be public so
     * Livewire keeps it between requests (protected props are reset).
     */
    public ?string $originalStateHash = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),

            // Actions\DeleteAction::make()
            //     ->visible(fn () => auth()->user()->hasRole('admin')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', [
            'record' => $this->record,
        ]);
    }

    protected function afterFill(): void
    {
        $this->originalStateHash = $this->hashState($this->data);
    }

    /**
     * If nothing changed: no validation, no delete of endorsement/invitation,
     * no "Saved" toast, no afterSave notification.
     */
    protected function beforeValidate(): void
    {
        if ($this->originalStateHash === null) {
            return;
        }

        if ($this->hashState($this->data) !== $this->originalStateHash) {
            return;
        }

        $this->redirect($this->getRedirectUrl());

        $this->halt();
    }

    protected function hashState(array $state): string
    {
        return md5(json_encode($this->normalizeState($state)));
    }

    protected function normalizeState(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn ($v) => $this->normalizeState($v), $value);
        }

        if ($value === null || $value === false) {
            return '';
        }

        if ($value === true) {
            return '1';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return json_encode($value);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['has_endorsement'])) {
            $this->record->endorsement()?->delete();
            unset($data['endorsement']);
        }

        unset($data['has_endorsement']);

        if (empty($data['has_invitation'])) {
            $this->record->invitation()?->delete();
            unset($data['invitation']);
        }

        unset($data['has_invitation']);

        if ($this->record->status === 'completed') {
            $data['status'] = 'completed';
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $r = $this->record;

        $userId = \DB::table('students')->where('id', $r->student_id)->value('user_id');
        if (! $userId) return;

        $message = null;
        if ($r->wasChanged('follow_up_required') && $r->follow_up_required) {
            $message = 'A follow-up is required for your referral.';
        } elseif ($r->wasChanged('status')) {
            $message = 'Your referral status is now ' . $r->status . '.';
        }
        if (! $message) return;

        \DB::table('notifications')->insert([
            'id'              => (string) \Illuminate\Support\Str::uuid(),
            'type'            => 'App\\Notifications\\ReferralNotification',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id'   => $userId,
            'data'            => json_encode([
                'title'       => 'Referral Update',
                'message'     => $message,
                'referral_id' => $r->id,
            ]),
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        $data['endorsement']['name'] = $record->name;
        $data['endorsement']['course_and_year'] = $record->course_and_year;

        return $data;
    }
}