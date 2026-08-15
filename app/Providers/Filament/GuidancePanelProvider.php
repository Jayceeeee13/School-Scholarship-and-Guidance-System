<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use App\Filament\Widgets\LatestAppointmentsWidget;
use App\Filament\Widgets\SupportNeedsChartWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class GuidancePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('guidance')
            ->path('guidance')
            ->brandName('Guidance Portal')
            ->login()
            ->colors([
                'primary' => Color::Teal,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->resources([
                \App\Filament\Resources\CounselingAppointmentsResource::class,
                \App\Filament\Resources\CounselingLogformsResource::class,
                \App\Filament\Resources\CounselingTimeSlotResource::class,
                \App\Filament\Resources\AnecdotalsResource::class,
                \App\Filament\Resources\ReferralsResource::class,
                \App\Filament\Resources\SupportNeededResource::class,
                \App\Filament\Resources\ModeOfCounselingResource::class,
            ])
            ->pages([
                Pages\Dashboard::class,
            ])
            ->widgets([
                LatestAppointmentsWidget::class,
                SupportNeedsChartWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugin(FilamentFullCalendarPlugin::make());
    }
}
