<?php

namespace App\Filament\Resources\CounselingAppointmentsResource\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\CounselingAppointments;
use App\Models\InactiveDate;
use Carbon\Carbon;

class CounselingAppointmentsCalendar extends FullCalendarWidget
{
    public function fetchEvents(array $fetchInfo): array
    {
        $events = [];

        $userRole = auth()->user()?->role?->name;

        if (in_array(strtolower($userRole ?? ''), ['guidance', 'admin'])) {
            $events = CounselingAppointments::query()
                ->with(['timeSlot', 'modeOfCounseling'])
                ->whereBetween('counseling_date', [$fetchInfo['start'], $fetchInfo['end']])
                ->get()
                ->map(function (CounselingAppointments $appointment) {
                    $color = match ($appointment->status) {
                        'pending'  => '#f59e0b',
                        'approved' => '#10b981',
                        'rejected' => '#ef4444',
                        default    => '#6b7280',
                    };

                    $name     = "{$appointment->first_name} {$appointment->last_name}";
                    $timeSlot = $appointment->timeSlot?->name ?? 'No Time';
                    $dateTime = Carbon::parse($appointment->counseling_date);

                    return [
                        'id'              => $appointment->id,
                        'title'           => "{$name} - {$timeSlot}",
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

        // Adjust this to whatever behavior you want on click
        // e.g. redirect to appointment detail page, open a modal, etc.
        $this->redirect('/admin-calendar/appointment/' . $id);
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