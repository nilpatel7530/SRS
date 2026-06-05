<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('admin.dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h2 class="form-title">Sign In</h2>
    <p class="form-subtitle">Access your account to continue</p>

    <form wire:submit="login">
        <!-- Email Address -->
        <div class="mb-4">
            <label class="label-premium">EMAIL ADDRESS</label>
            <div class="input-wrapper">
                <i class="fas fa-envelope input-icon"></i>
                <input wire:model="form.email" type="email" class="input-premium" placeholder="name@company.com" required autofocus autocomplete="username">
            </div>
            @error('form.email') <span class="text-danger small d-block" style="margin-top: -15px; margin-bottom: 15px;">{{ $message }}</span> @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label class="label-premium">PASSWORD</label>
            <div class="input-wrapper">
                <i class="fas fa-lock input-icon"></i>
                <input wire:model="form.password" type="password" class="input-premium" placeholder="••••••••" required autocomplete="current-password">
            </div>
            @error('form.password') <span class="text-danger small d-block" style="margin-top: -15px; margin-bottom: 15px;">{{ $message }}</span> @enderror
        </div>

        <div class="form-options">
            <div class="remember-me">
                <input wire:model="form.remember" type="checkbox" id="remember" style="width: 18px; height: 18px; accent-color: var(--cel-red);">
                <label for="remember">Remember me</label>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="forgot-password" wire:navigate>Forgot Password?</a>
            @endif
        </div>

        <button type="submit" class="btn-cel">
            <span wire:loading.remove wire:target="login">Sign In</span>
            <span wire:loading wire:target="login">
                <i class="fas fa-circle-notch fa-spin"></i> Authenticating...
            </span>
        </button>
    </form>

    <div class="form-footer">
        Don't have an account? <a href="#">Contact Administrator</a>
    </div>
</div>
