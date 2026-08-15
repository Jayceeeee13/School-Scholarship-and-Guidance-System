<?php

namespace App\Filament\Resources\CalendarResource\Pages;

use App\Filament\Resources\CalendarResource;
use App\Models\InactiveDate;
use App\Models\CounselingAppointments;
use Filament\Resources\Pages\Page;
use Filament\Actions;
use Filament\Notifications\Notification;
use Carbon\Carbon;
use Livewire\Attributes\Url;

class ManageDatePage extends Page
{
    protected static string $resource = CalendarResource::class;
    protected static string $view = 'filament.resources.calendar-resource.pages.manage-date-page';
    protected static ?string $title = 'Manage Date';

    #[Url]
    public string $date = '';

    public $events = [];
    public $appointments = [];

    // form fields for adding a new event
    public string $newTitle = '';
    public ?string $newReason = null;

    public function mount(): void
    {
        if (empty($this->date)) {
            $this->date = now()->format('Y-m-d');
        }
        $this->loadData();
    }

    public function loadData(): void
    {
        $this->events = InactiveDate::getEventsForDate($this->date);

        $this->appointments = CounselingAppointments::query()
            ->with(['timeSlot', 'modeOfCounseling', 'supportNeeded'])
            ->whereDate('counseling_date', $this->date)
            ->orderBy('time_slot_id')
            ->get();
    }

    public function addEvent(): void
    {
        $this->validate([
            'newTitle' => 'required|string|max:255',
            'newReason' => 'nullable|string|max:255',
        ]);

        InactiveDate::create([
            'date'   => $this->date,
            'title'  => $this->newTitle,
            'reason' => $this->newReason,
        ]);

        $this->newTitle = '';
        $this->newReason = null;
        $this->loadData();

        Notification::make()
            ->title('Event added')
            ->success()
            ->send();
    }

    public function deleteEvent(int $eventId): void
    {
        InactiveDate::where('id', $eventId)->delete();
        $this->loadData();

        Notification::make()
            ->title('Event removed')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back to Calendar')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url('/admin-calendar'),
        ];
    }

    public function getTitle(): string
    {
        return 'Manage Date: ' . Carbon::parse($this->date)->format('F j, Y');
    }
}