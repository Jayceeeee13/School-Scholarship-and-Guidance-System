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

//     public static function shouldRegisterNavigation(): bool
// {
//     return auth()->user()->hasAnyRole(['admin', 'guidance', 'scholarship']);
// }

public static function canAccess(): bool
{
    return auth()->user()->hasAnyRole(['admin', 'guidance', 'scholarship']);
}
}