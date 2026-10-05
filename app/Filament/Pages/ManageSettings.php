<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ManageSettings extends Page
{
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-s-cog-6-tooth';

    protected static ?string $navigationLabel = 'Settings';

    protected static string $view = 'filament.pages.manage-settings';

    // Remove the duplicate title
    protected static ?string $title = 'Settings';

    protected static ?int $navigationSort = 100;

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'guidance', 'scholarship']);
    }

    /**
     * Number of configuration links shown under each settings card.
     * Update this whenever a link is added/removed from the Blade view,
     * so the card header count stays accurate.
     */
    public function countConfigured(string $category): int
    {
        return match ($category) {
            'counseling'  => 4, // Support Types, Counseling Modes, Time Slots, Relationship Types
            'scholarship' => 5, // Application Types, Requirements, Exam Categories, Application Period, Submission Period, Exam Period
            'general'     => 7, // Departments, Programs, Genders, School Positions, Roles, Terms, Archived Records
            default       => 0,
        };
    }
}