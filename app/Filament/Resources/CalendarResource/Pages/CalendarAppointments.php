<?php

namespace App\Filament\Resources\CalendarResource\Pages;

use App\Filament\Resources\CalendarResource;
use App\Filament\Resources\CounselingAppointmentsResource;
use Filament\Actions;
use Filament\Resources\Pages\Page;

class CalendarAppointments extends Page
{
    protected static string $resource = CalendarResource::class;

    protected static string $view = 'filament.resources.counseling-appointments-resource.pages.calendar-appointments';

    protected static ?string $title = 'Calendar';

    protected function getHeaderActions(): array
    {
        return [
            // Actions\Action::make('list')
            //     ->label('List View')
            //     ->icon('heroicon-o-list-bullet')
            //     ->color('gray')
            //     ->url(fn (): string => CounselingAppointmentsResource::getUrl('index')),

            // Actions\Action::make('create')
            //     ->label('New Appointment')
            //     ->icon('heroicon-o-plus')
            //     ->color('success')
            //     ->url(fn (): string => CounselingAppointmentsResource::getUrl('create')),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\CounselingAppointmentsResource\Widgets\CounselingAppointmentsCalendar::class,
        ];
    }
}