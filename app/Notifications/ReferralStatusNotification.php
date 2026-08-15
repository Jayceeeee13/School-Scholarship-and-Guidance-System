<?php

namespace App\Notifications;

use App\Models\Referrals;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReferralStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Referrals $referral,
        protected string $type
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $date = $this->referral->date->format('F d, Y');
        $name = $this->referral->name;

        $config = match ($this->type) {
            'new_referral' => [
                'title' => '📋 New Referral Submitted',
                'body'  => "{$name} was referred by {$this->referral->referred_by} on {$date}.",
                'icon'  => 'heroicon-o-arrow-right-circle',
                'color' => 'info',
            ],
            'approved' => [
                'title' => '✅ Referral Approved',
                'body'  => "Referral for {$name} on {$date} was approved.",
                'icon'  => 'heroicon-o-check-circle',
                'color' => 'success',
            ],
            'rejected' => [
                'title' => '❌ Referral Rejected',
                'body'  => "Referral for {$name} on {$date} was rejected.",
                'icon'  => 'heroicon-o-x-circle',
                'color' => 'danger',
            ],
            default => [
                'title' => 'Referral Update',
                'body'  => "Referral for {$name} on {$date} was updated.",
                'icon'  => 'heroicon-o-bell',
                'color' => 'gray',
            ],
        };

        return FilamentNotification::make()
            ->title($config['title'])
            ->body($config['body'])
            ->icon($config['icon'])
            ->color($config['color'])
            ->getDatabaseMessage();
    }

    /** Notify all Admin/Guidance staff — mirrors AppointmentStatusNotification::send() */
    public static function send(Referrals $referral, string $type): void
    {
        $recipients = \App\Models\User::whereHas('role', fn ($q) =>
            $q->whereIn('name', ['Admin', 'Guidance'])
        )->get();

        foreach ($recipients as $user) {
            $user->notify(new self($referral, $type));
        }
    }
}