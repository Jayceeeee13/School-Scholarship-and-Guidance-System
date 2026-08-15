<?php

namespace App\Livewire\Auth;

use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;


class ForgotPassword extends \Livewire\Component
{
    use \Filament\Actions\Concerns\InteractsWithActions;

    public string $email = '';
    public string $otp = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Steps: 'email' | 'otp' | 'reset' | 'done'
    public string $step = 'email';

    public int $resendCooldown = 0;
    public string $maskedEmail = '';

    public function sendOtp(): void
    {
        $this->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'No account found with that email address.',
        ]);

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        User::where('email', $this->email)->update([
            'otp'            => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($this->email)->send(new OtpMail($otp));

        // Mask the email for display e.g. ed****@gmail.com
        $parts = explode('@', $this->email);
        $this->maskedEmail = substr($parts[0], 0, 2) . str_repeat('*', max(strlen($parts[0]) - 2, 3)) . '@' . $parts[1];

        $this->resendCooldown = 60;
        $this->step = 'otp';
        $this->dispatch('otp-sent');
    }

    public function verifyOtp(): void
    {
        $this->validate(['otp' => 'required|digits:6']);

        $user = User::where('email', $this->email)
            ->where('otp', $this->otp)
            ->where('otp_expires_at', '>=', now())
            ->first();

        if (! $user) {
            $this->addError('otp', 'Invalid or expired OTP. Please try again.');
            return;
        }

        $this->step = 'reset';
    }

    public function resetPassword(): void
    {
        $this->validate([
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        User::where('email', $this->email)->update([
            'password'       => Hash::make($this->password),
            'otp'            => null,
            'otp_expires_at' => null,
        ]);

        $this->step = 'done';
    }

    public function resendOtp(): void
{
    $this->otp = '';
    $this->resendCooldown = 60;
    $this->sendOtpToEmail();
    $this->dispatch('otp-sent'); // add this line
}
    private function sendOtpToEmail(): void
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        User::where('email', $this->email)->update([
            'otp'            => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($this->email)->send(new OtpMail($otp));
    }

    public function backToEmail(): void
    {
        $this->step = 'email';
        $this->otp = '';
        $this->reset('otp');
        $this->resetErrorBag();
    }

   public function render()
{
    return view('livewire.auth.forgot-password')
        ->layout('filament-panels::components.layout.simple');
}
    
}

