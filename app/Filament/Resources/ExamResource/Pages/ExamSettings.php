<?php

namespace App\Filament\Resources\ExamResource\Pages;

use Illuminate\Contracts\Support\Htmlable;

/**
 * Only one exam is managed, so its page is called "Exam Management" instead of
 * Filament's default "Edit Exam". The form, saving and everything else are
 * inherited from EditExam.
 */
class ExamSettings extends EditExam
{
    public function getTitle(): string | Htmlable
    {
        return 'Exam Management';
    }

    public function getBreadcrumbs(): array
    {
        return ['Exam'];
    }
}