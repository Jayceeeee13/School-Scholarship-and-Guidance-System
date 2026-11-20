<?php

namespace App\Filament\Resources\CounselingAppointmentsResource\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Filament\Resources\CounselingAppointmentsResource;
use App\Filament\Resources\ReferralsResource;
use App\Models\CounselingAppointments;
use App\Models\InactiveDate;
use App\Models\Referrals;
use Carbon\Carbon;
use Livewire\Attributes\On;

class CounselingAppointmentsCalendar extends FullCalendarWidget
{
    public string $filter = 'all'; // all | appointments | followups | invitations

    #[On('calendar-filter')]
    public function setFilter(string $mode): void
    {
        $this->filter = in_array($mode, ['all', 'appointments', 'followups', 'invitations'], true) ? $mode : 'all';
        $this->refreshRecords();
    }

    public function fetchEvents(array $fetchInfo): array
    {
        $events      = [];
        $invitations = [];

        // hasAnyRole() checks both the roles pivot and the legacy role_id.
        if (auth()->user()?->hasAnyRole(['guidance', 'admin'])) {

            // ── Counseling appointments & follow-ups ─────────────────────────
            if ($this->filter !== 'invitations') {
                $events = CounselingAppointments::query()
                    ->with(['timeSlot', 'modeOfCounseling'])
                    ->whereNull('archived_at')
                    ->where('status', '!=', 'cancelled')
                    // A follow-up is linked to a parent appointment, or was scheduled
                    // from a referral (referral_id) or from a logform (source_logform_id).
                    ->when($this->filter === 'followups', fn ($q) => $q->where(
                        fn ($w) => $w->whereNotNull('parent_appointment_id')
                            ->orWhereNotNull('referral_id')
                            ->orWhereNotNull('source_logform_id')
                    ))
                    ->when($this->filter === 'appointments', fn ($q) => $q
                        ->whereNull('parent_appointment_id')
                        ->whereNull('referral_id')
                        ->whereNull('source_logform_id'))
                    ->whereBetween('counseling_date', [$fetchInfo['start'], $fetchInfo['end']])
                    ->get()
                    ->map(function (CounselingAppointments $appointment) {
                        $isFollowUp = $appointment->isFollowUp()
                            || filled($appointment->referral_id)
                            || filled($appointment->source_logform_id);

                        $color = $isFollowUp
                            ? '#3b82f6'
                            : match ($appointment->status) {
                                'pending'   => '#f59e0b',
                                'approved'  => '#10b981',
                                'rejected'  => '#ef4444',
                                'completed' => '#6b7280',
                                default     => '#9ca3af',
                            };

                        $name     = "{$appointment->first_name} {$appointment->last_name}";
                        $timeSlot = $appointment->timeSlot?->name ?? 'No Time';
                        $dateTime = Carbon::parse($appointment->counseling_date)
                            ->setTimezone(config('app.timezone'));

                        return [
                            'id'              => $appointment->id,
                            'title'           => ($isFollowUp ? '↻ Follow-up: ' : '') . "{$name} - {$timeSlot}",
                            'start'           => $dateTime->format('Y-m-d\TH:i:s'),
                            'end'             => $dateTime->copy()->addHour()->format('Y-m-d\TH:i:s'),
                            'backgroundColor' => $color,
                            'borderColor'     => $color,
                            'textColor'       => '#ffffff',
                        ];
                    })->toArray();
            }

            // ── Referral invitations ─────────────────────────────────────────
            // Declined invitations free their slot, so they are not shown
            // (same rule SlotAvailability uses). Archived referrals are skipped.
            if (in_array($this->filter, ['all', 'invitations'], true)) {
                $rangeStart = Carbon::parse($fetchInfo['start'])->toDateString();
                $rangeEnd   = Carbon::parse($fetchInfo['end'])->toDateString();

                $invitations = Referrals::query()
                    ->whereNull('archived_at')
                    ->whereHas('invitation', function ($q) use ($rangeStart, $rangeEnd) {
                        $q->where('status', '!=', 'declined')
                            ->whereBetween('session_date', [$rangeStart, $rangeEnd]);
                    })
                    ->with(['invitation.timeSlot'])
                    ->get()
                    ->map(function (Referrals $referral) {
                        $invitation = $referral->invitation;

                        $timeSlot = $invitation->timeSlot?->name ?? 'No Time';
                        $dateTime = Carbon::parse($invitation->session_date)
                            ->setTimezone(config('app.timezone'));

                        // Violet so invitations stand out from appointment statuses.
                        $color = $invitation->status === 'accepted' ? '#6d28d9' : '#8b5cf6';

                        return [
                            'id'              => 'referral-' . $referral->id,
                            'title'           => "✉ Invitation: {$referral->name} - {$timeSlot}",
                            'start'           => $dateTime->format('Y-m-d\TH:i:s'),
                            'end'             => $dateTime->copy()->addHour()->format('Y-m-d\TH:i:s'),
                            'backgroundColor' => $color,
                            'borderColor'     => $color,
                            'textColor'       => '#ffffff',
                        ];
                    })->toArray();
            }
        }

        $inactiveDates = InactiveDate::whereBetween('date', [
                $fetchInfo['start'],
                $fetchInfo['end']
            ])
            ->get()
            ->map(function ($inactive) {
                return [
                    'id'              => 'inactive-' . $inactive->id,
                    'title'           => '🚫 ' . ($inactive->reason ?? 'Unavailable'),
                    'start'           => $inactive->date->format('Y-m-d'),
                    'end'             => $inactive->date->format('Y-m-d'),
                    'allDay'          => true,
                    'display'         => 'background',
                    'backgroundColor' => '#fecaca',
                    'borderColor'     => '#ef4444',
                ];
            })->toArray();

        return array_merge($events, $invitations, $inactiveDates);
    }

    public function onDateSelect(string $start, ?string $end, bool $allDay, ?array $view, ?array $resource): void
    {
        $date = Carbon::parse($start)->format('Y-m-d');
        $this->redirect('/admin-calendar/date/' . $date);
    }

    public function onEventClick(array $event): void
    {
        $id = $event['id'] ?? null;

        // Ignore clicks on inactive-date background blocks
        if (! $id || str_starts_with((string) $id, 'inactive-')) {
            return;
        }

        // Referral invitation -> open that referral on its Invitation tab
        if (str_starts_with((string) $id, 'referral-')) {
            $this->redirect(
                ReferralsResource::getUrl('edit', [
                    'record' => (int) substr((string) $id, strlen('referral-')),
                    'tab'    => '-invitation-tab',
                ])
            );

            return;
        }

        $this->redirect(
            CounselingAppointmentsResource::getUrl('edit', ['record' => $id])
        );
    }

    public function config(): array
    {
        return [
            'headerToolbar' => [
                'left'   => 'prev,next today',
                'center' => 'title',
                'right'  => 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
            ],
            'initialView' => 'dayGridMonth',
            'editable'    => false,
            'selectable'  => true,
            'navLinks'    => false,
            'height'      => 'auto',
        ];
    }
}