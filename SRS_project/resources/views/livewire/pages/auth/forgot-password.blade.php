<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div>
    <h2 class="form-title">Reset Password</h2>
    <p class="form-subtitle">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.') }}
    </p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-success" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink">
        <!-- Email Address -->
        <div class="mb-4">
            <label class="label-premium">EMAIL ADDRESS</label>
            <div class="input-wrapper">
                <i class="fas fa-envelope input-icon"></i>
                <input wire:model="email" type="email" class="input-premium" placeholder="name@company.com" required autofocus autocomplete="username">
            </div>
            @error('email') <span class="text-danger small d-block" style="margin-top: -15px; margin-bottom: 15px;">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn-cel">
            <span wire:loading.remove wire:target="sendPasswordResetLink">
                {{ __('Email Password Reset Link') }}
            </span>
            <span wire:loading wire:target="sendPasswordResetLink">
                <i class="fas fa-circle-notch fa-spin"></i> Sending link...
            </span>
        </button>
    </form>
    
    <div class="form-footer">
        Remembered your password? <a href="{{ route('login') }}" wire:navigate>Back to Login</a>
    </div>
</div>
