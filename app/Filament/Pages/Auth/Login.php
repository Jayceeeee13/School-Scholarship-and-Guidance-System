<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    protected function getEmailFormComponent(): TextInput
    {
        return parent::getEmailFormComponent()
            ->extraInputAttributes(['autocomplete' => 'off']);
    }

    protected function getPasswordFormComponent(): TextInput
    {
        return parent::getPasswordFormComponent()
            ->extraInputAttributes(['autocomplete' => 'new-password']);
    }

    public function getSubheading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return new \Illuminate\Support\HtmlString(
            '<div class="text-center mt-4">
                <a href="' . url('/') . '" class="text-sm font-medium text-primary-600 hover:text-primary-500 underline">
                    ← Back to Portal
                </a>
            </div>'
        );
    }
}