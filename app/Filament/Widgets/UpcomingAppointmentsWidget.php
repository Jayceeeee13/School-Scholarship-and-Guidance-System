<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\CounselingAppointments;
use Carbon\Carbon;

class UpcomingAppointmentsWidget extends Widget
{
    protected static string $view = 'filament.widgets.upcoming-appointments-widget';

    // Right column – sits beside CombinedStatsWidget
    protected int | string | array $columnSpan = [
    'default' => 'full',
    'md'      => 2,
];
protected static ?int $sort = 6;

    public function getAppointments()
    {
        return CounselingAppointments::query()
            ->with(['timeSlot', 'modeOfCounseling'])
            ->whereBetween('counseling_date', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])
            ->orderBy('counseling_date')
            ->orderBy('time_slot_id')
            ->get();
    }

    public function getTodayAppointments()
    {
        return CounselingAppointments::query()
            ->with(['timeSlot'])
            ->whereDate('counseling_date', Carbon::today())
            ->orderBy('time_slot_id')
            ->get();
    }

    public static function canView(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isGuidance();
    }
}