<?php

namespace App\Filament\Widgets;

use App\Models\Applicant;
use App\Models\CounselingAppointments;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Filament\Resources\CounselingAppointmentsResource;

/**
 * Merged stats widget: shows Applicant stats AND Appointment stats
 * in a single card so they share one row instead of two separate rows.
 *
 * Place this file at:
 *   app/Filament/Widgets/CombinedStatsWidget.php
 *
 * Then register ONLY CombinedStatsWidget in Dashboard.php
 * and REMOVE ApplicantsStatsWidget & AppointmentsStatsWidget from the list.
 */
class CombinedStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    // Left column – sits beside UpcomingAppointmentsWidget
    protected int | string | array $columnSpan = 'full';


    protected function getStats(): array
    {
        $stats = [];

        // ── Applicant stats (only for Admin / Scholarship) ──────────────────
        if (auth()->user()->isAdmin() || auth()->user()->isScholarship()) {
            $total   = Applicant::count();
            $pending = Applicant::where('status', 'pending')->count();

            $stats[] = Stat::make('Total Institutional Scholarship Applicants', $total)
                ->description('All time applicants')
                ->icon('heroicon-o-users')
                ->color('primary');

            $stats[] = Stat::make('Pending Institutional Applications', $pending)
                ->description('Awaiting review')
                ->icon('heroicon-o-clock')
                ->color('warning');
        }

        // ── Appointment stats (only for Admin / Guidance) ────────────────────
        if (auth()->user()->isAdmin() || auth()->user()->isGuidance()) {
            $pendingAppts = CounselingAppointments::where('status', 'pending')->count();

            $stats[] = Stat::make('Pending Counseling Appointments', $pendingAppts)
                ->description('Awaiting approval')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([7, 12, 18, 15, 22, 28, $pendingAppts])
                ->url(CounselingAppointmentsResource::getUrl('index'));
        }

        return $stats;
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user->isAdmin() || $user->isScholarship() || $user->isGuidance();
    }
}