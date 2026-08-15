<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use App\Livewire\Auth\ForgotPassword;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->profile()
            ->brandName('Green Valley College Foundation Inc.')
            ->login()
            ->authGuard('web')
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->passwordReset(ForgotPassword::class)
            ->userMenuItems([
                MenuItem::make()
                    ->label('Settings')
                    ->url(fn () => \App\Filament\Pages\ManageSettings::getUrl())
                    ->icon('heroicon-o-cog-6-tooth'),
                MenuItem::make()
                    ->label('Profile')
                    ->url(fn () => '/admin/profile')
                    ->icon('heroicon-o-user-circle'),
            ])
            ->colors([
                'primary' => Color::Green,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                // ✅ Use YOUR custom Dashboard, not Filament's built-in one
                \App\Filament\Pages\Dashboard::class,
            ])
            ->navigationGroups([
                'Generals',
    'Scholarship Management',
    'Guidance Management',
    'Exam Management', 
            ])
            // ✅ No widgets — everything is rendered in the custom blade
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
                ->widgets([
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
            ->plugin(FilamentFullCalendarPlugin::make()
            ->selectable(true)
            );
    }

    public function boot(): void
{
    FilamentView::registerRenderHook(
        'panels::body.end',
        fn (): string => request()->is('admin*') ? '<style>
        /* ========================================
           SIDEBAR - MEDIUM GREEN BACKGROUND
           ======================================== */
        aside.fi-sidebar {
            background-color: #059669 !important;
        }

        nav.fi-sidebar-nav {
            background-color: transparent !important;
        }

        .fi-sidebar-item-button,
        .fi-sidebar-item a {
            color: white !important;
        }

        .fi-sidebar-item-button svg,
        .fi-sidebar-item a svg {
            color: white !important;
        }

        .fi-sidebar-item-label {
            color: white !important;
        }

        .fi-sidebar-item-button:hover,
        .fi-sidebar-item a:hover {
            background-color: rgba(255, 255, 255, 0.15) !important;
            border-radius: 0.5rem !important;
        }

        /* ========================================
           ACTIVE SIDEBAR ITEM - WHITE BACKGROUND
           ======================================== */
        .fi-sidebar-item-button.bg-gray-100,
        .fi-sidebar-item-button.dark\:bg-white\/5,
        li.fi-sidebar-item a[aria-current="page"],
        .fi-sidebar-item a.bg-gray-100,
        .fi-sidebar-item.fi-active > button,
        .fi-sidebar-item.fi-active > a {
            background-color: white !important;
            color: #059669 !important;
            border-radius: 0.5rem !important;
        }

        .fi-sidebar-item-button.bg-gray-100 svg,
        .fi-sidebar-item-button.dark\:bg-white\/5 svg,
        li.fi-sidebar-item a[aria-current="page"] svg,
        .fi-sidebar-item a.bg-gray-100 svg,
        .fi-sidebar-item.fi-active > button svg,
        .fi-sidebar-item.fi-active > a svg {
            color: #059669 !important;
        }

        .fi-sidebar-item-button.bg-gray-100 .fi-sidebar-item-label,
        .fi-sidebar-item-button.dark\:bg-white\/5 .fi-sidebar-item-label,
        li.fi-sidebar-item a[aria-current="page"] .fi-sidebar-item-label,
        .fi-sidebar-item a.bg-gray-100 .fi-sidebar-item-label,
        .fi-sidebar-item.fi-active > button .fi-sidebar-item-label,
        .fi-sidebar-item.fi-active > a .fi-sidebar-item-label {
            color: #059669 !important;
            font-weight: 700 !important;
        }

        .fi-sidebar-item-button.bg-gray-100:hover,
        .fi-sidebar-item-button.dark\:bg-white\/5:hover,
        li.fi-sidebar-item a[aria-current="page"]:hover,
        .fi-sidebar-item a.bg-gray-100:hover,
        .fi-sidebar-item.fi-active > button:hover,
        .fi-sidebar-item.fi-active > a:hover {
            background-color: #f3f4f6 !important;
            color: #059669 !important;
        }

        .fi-sidebar-item-button.bg-gray-100:hover svg,
        .fi-sidebar-item-button.bg-gray-100:hover .fi-sidebar-item-label,
        .fi-sidebar-item.fi-active > button:hover svg,
        .fi-sidebar-item.fi-active > button:hover .fi-sidebar-item-label {
            color: #059669 !important;
        }

        .fi-sidebar-item .fi-badge {
            background-color: white !important;
            color: #059669 !important;
        }

        .fi-sidebar-item.fi-active .fi-badge,
        .fi-sidebar-item-button.bg-gray-100 .fi-badge {
            background-color: #059669 !important;
            color: white !important;
        }

        .fi-sidebar-group-label {
            color: rgba(255, 255, 255, 0.8) !important;
        }

        .fi-sidebar-group {
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        .fi-sidebar-group-button {
            color: white !important;
        }

        .fi-sidebar-group-button svg {
            color: white !important;
        }

        /* ========================================
           SIDEBAR HEADER - DARKER GREEN
           ======================================== */
        .fi-sidebar-header {
            background-color: #047857 !important;
        }

        .fi-sidebar-header *,
        .fi-sidebar-header a,
        .fi-sidebar-header span {
            color: white !important;
        }

        .fi-sidebar-header img {
            filter: brightness(0) invert(1);
        }

        /* ========================================
           SIDEBAR COLLAPSE BUTTON
           ======================================== */
        .fi-sidebar-collapse-button {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.1) !important;
        }

        .fi-sidebar-collapse-button:hover {
            background-color: rgba(255, 255, 255, 0.2) !important;
        }

        .fi-sidebar-collapse-button svg {
            color: white !important;
        }

        /* ========================================
           TOP BAR - MEDIUM GREEN
           ======================================== */
        div.fi-topbar {
            background-color: #059669 !important;
        }

        .fi-topbar nav {
            background-color: transparent !important;
        }

        .fi-topbar svg {
            color: white !important;
        }

        .fi-topbar button {
            color: white !important;
        }

        /* ========================================
           CLOCK BEFORE AVATAR IN TOPBAR
           ======================================== */
        .fi-topbar nav {
            display: flex !important;
            align-items: center !important;
        }

        .fi-topbar nav > *:last-child {
            order: 2 !important;
        }

        .fi-topbar nav > div[x-data] {
            order: 1 !important;
        }

        /* ========================================
           TABLE HEADER TOOLBAR - MEDIUM GREEN
           ======================================== */
        .fi-ta-header-toolbar {
            background-color: #059669 !important;
        }

        .fi-ta-header-toolbar .fi-dropdown-trigger,
        .fi-ta-header-toolbar button[type="button"] {
            background-color: white !important;
            color: #1f2937 !important;
        }

        .fi-ta-header-toolbar .fi-dropdown-trigger svg,
        .fi-ta-header-toolbar button[type="button"] svg {
            color: #1f2937 !important;
        }

        .fi-ta-header-toolbar input {
            background-color: white !important;
            color: #1f2937 !important;
        }

        .fi-ta-header-toolbar .fi-ta-selection-indicator {
            color: white !important;
        }

        .fi-ta-header-toolbar a {
            color: white !important;
        }

        .fi-dropdown-panel {
            background-color: white !important;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1) !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 0.5rem !important;
        }

        .fi-dropdown-panel * {
            color: #1f2937 !important;
            background-color: transparent !important;
        }

        .fi-dropdown-list-item:hover {
            background-color: #f3f4f6 !important;
        }

        .fi-dropdown-list-item-color-danger,
        .fi-dropdown-list-item-color-danger * {
            color: #dc2626 !important;
        }

        .fi-dropdown-panel hr {
            border-color: #e5e7eb !important;
        }

        .fi-simple-layout {
            background-image: url("/images/gvc.png") !important;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            min-height: 100vh !important;
        }

        .fi-simple-layout::before {
            content: "" !important;
            position: fixed !important;
            inset: 0 !important;
            background: rgba(0, 0, 0, 0.4) !important;
            z-index: 0 !important;
        }

        .fi-simple-main {
            position: relative !important;
            z-index: 1 !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
            border-radius: 1rem !important;
        }

        [x-cloak] { display: none !important; }

        </style>' : ''
    );

    FilamentView::registerRenderHook(
        PanelsRenderHook::USER_MENU_BEFORE,  // 👈 this places it right before the JS avatar
        fn (): \Illuminate\Contracts\View\View => view('filament.topbar-clock'),
    );
}
}