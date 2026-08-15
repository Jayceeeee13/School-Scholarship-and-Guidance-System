<?php
// app/Notifications/AppointmentStatusNotification.php

namespace App\Notifications;

use App\Models\CounselingAppointments;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AppointmentStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected CounselingAppointments $appointment,
        protected string $type
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $date     = $this->appointment->counseling_date->format('F d, Y');
        $fullName = $this->appointment->full_name;

        $config = match ($this->type) {
            'new_appointment' => [
                'title' => '🗓️ New Appointment Request',
                'body'  => "{$fullName} submitted a new counseling appointment on {$date}.",
                'icon'  => 'heroicon-o-calendar-days',
                'color' => 'info',
            ],
            'cancelled' => [
                'title' => 'Appointment Cancelled',
                'body'  => "{$fullName} cancelled their appointment on {$date}.",
                'icon'  => 'heroicon-o-x-circle',
                'color' => 'danger',
            ],
            'approved' => [
                'title' => '✅ Appointment Approved',
                'body'  => "Appointment for {$fullName} on {$date} was approved.",
                'icon'  => 'heroicon-o-check-circle',
                'color' => 'success',
            ],
            'rejected' => [
                'title' => '❌ Appointment Rejected',
                'body'  => "Appointment for {$fullName} on {$date} was rejected.",
                'icon'  => 'heroicon-o-x-circle',
                'color' => 'danger',
            ],
            'follow_up_scheduled' => [
                'title' => '🔁 Follow-up Appointment Scheduled',
                'body'  => "A follow-up appointment for {$fullName} on {$date} has been scheduled.",
                'icon'  => 'heroicon-o-arrow-path-rounded-square',
                'color' => 'info',
            ],
            default => [
                'title' => 'Appointment Update',
                'body'  => "Appointment for {$fullName} on {$date} was updated.",
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

    /** Notify all Admin/Guidance staff — mirrors ReferralInvitationResponseNotification::send() */
    public static function send(CounselingAppointments $appointment, string $type): void
    {
        $recipients = \App\Models\User::whereHas('role', fn ($q) =>
            $q->whereIn('name', ['Admin', 'Guidance'])
        )->get();

        foreach ($recipients as $user) {
            $user->notify(new self($appointment, $type));
        }
    }
}