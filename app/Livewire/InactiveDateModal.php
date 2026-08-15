<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\InactiveDate;
use Livewire\Attributes\On;

class InactiveDateModal extends Component
{
    public bool $isOpen = false;
    public ?string $selectedDate = null;
    public ?string $reason = null;

    #[On('openInactiveDateModal')]
    public function openModal($date): void
    {
        $this->selectedDate = $date;
        $this->reason = null;
        $this->isOpen = true;
    }

    public function save(): void
    {
        InactiveDate::create([
            'date'   => $this->selectedDate,
            'reason' => $this->reason,
        ]);

        $this->isOpen = false;
        $this->dispatch('filament-fullcalendar--refresh');
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->selectedDate = null;
        $this->reason = null;
    }

    public function render()
    {
        return view('livewire.inactive-date-modal');
    }
}