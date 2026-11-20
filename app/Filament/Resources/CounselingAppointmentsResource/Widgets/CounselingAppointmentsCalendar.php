<?php

namespace App\Filament\Resources\CounselingAppointmentsResource\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Filament\Resources\CounselingAppointmentsResource;
use App\Models\CounselingAppointments;
use App\Models\InactiveDate;
use Carbon\Carbon;

class CounselingAppointmentsCalendar extends FullCalendarWidget
{
    public function fetchEvents(array $fetchInfo): array
    {
        $events = [];

        // hasAnyRole() checks both the roles pivot and the legacy role_id.
        if (auth()->user()?->hasAnyRole(['guidance', 'admin'])) {
            $events = CounselingAppointments::query()
                ->with(['timeSlot', 'modeOfCounseling'])
                ->whereNull('archived_at')
                ->where('status', '!=', 'cancelled')
                ->whereBetween('counseling_date', [$fetchInfo['start'], $fetchInfo['end']])
                ->get()
                ->map(function (CounselingAppointments $appointment) {
                    $isFollowUp = $appointment->isFollowUp();

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
                    $dateTime = Carbon::parse($appointment->counseling_date);

                    return [
                        'id'              => $appointment->id,
                        'title'           => ($isFollowUp ? '↻ Follow-up: ' : '') . "{$name} - {$timeSlot}",
                        'start'           => $dateTime->toIso8601String(),
                        'end'             => $dateTime->copy()->addHour()->toIso8601String(),
                        'backgroundColor' => $color,
                        'borderColor'     => $color,
                        'textColor'       => '#ffffff',
                    ];
                })->toArray();
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

        return array_merge($events, $inactiveDates);
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
        if (!$id || str_starts_with((string) $id, 'inactive-')) {
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