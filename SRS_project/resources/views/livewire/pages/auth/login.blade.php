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
    <p class="login-box-msg">Sign in to start your session</p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-success" :status="session('status')" />

    <form wire:submit="login">
        <!-- Email Address -->
        <div class="input-group mb-3">
            <input wire:model="form.email" type="email" class="form-control" placeholder="Email" required autofocus autocomplete="username">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
        </div>
        @error('form.email') <span class="text-danger small">{{ $message }}</span> @enderror

        <!-- Password -->
        <div class="input-group mb-3 @error('form.email') mt-3 @enderror">
            <input wire:model="form.password" type="password" class="form-control" placeholder="Password" required autocomplete="current-password">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>
        @error('form.password') <span class="text-danger small">{{ $message }}</span> @enderror

        <div class="row mt-3">
            <div class="col-8">
                <div class="icheck-primary">
                    <input wire:model="form.remember" type="checkbox" id="remember">
                    <label for="remember">
                        Remember Me
                    </label>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-4">
                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </div>
            <!-- /.col -->
        </div>
    </form>

    @if (Route::has('password.request'))
        <p class="mb-1 mt-3">
            <a href="{{ route('password.request') }}" wire:navigate>I forgot my password</a>
        </p>
    @endif
</div>
