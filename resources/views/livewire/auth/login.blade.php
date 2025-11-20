<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        $user = \App\Models\User::where('email', $this->email)->first();

        if (!$user) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $authenticated = false;
        $inputValue = trim($this->password);

        // Check if input looks like a login code (exactly 6 alphanumeric characters)
        $isLoginCode = strlen($inputValue) === 6 && ctype_alnum($inputValue);

        // Try login code first if it matches the pattern
        if ($isLoginCode) {
            if ($user->isLoginCodeValid($inputValue)) {
                // Check if user already has password set
                if ($user->hasPasswordSet()) {
                    RateLimiter::hit($this->throttleKey());
                    throw ValidationException::withMessages([
                        'password' => 'You have already set your password. Please use your password to log in.',
                    ]);
                }
                // Authenticate user with login code
                Auth::login($user, $this->remember);
                $authenticated = true;
            } else {
                // Login code invalid - try as password instead
                if (Auth::attempt(['email' => $this->email, 'password' => $inputValue], $this->remember)) {
                    $authenticated = true;
                } else {
                    RateLimiter::hit($this->throttleKey());
                    throw ValidationException::withMessages([
                        'password' => 'Invalid login code or password.',
                    ]);
                }
            }
        } else {
            // Input doesn't look like login code, try as password
            if (Auth::attempt(['email' => $this->email, 'password' => $inputValue], $this->remember)) {
                $authenticated = true;
            } else {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'email' => __('auth.failed'),
                ]);
            }
        }

        $authenticatedUser = Auth::user();

        if ($authenticatedUser && !$authenticatedUser->hasVerifiedEmail()) {
            $this->redirectIntended(route('verification.notice', absolute: false));
            return;
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        // Check if password needs to be set
        if (!$authenticatedUser->hasPasswordSet()) {
            $this->redirectIntended(route('password.setup', absolute: false));
            return;
        }

        $this->redirectIntended(default: route('dashboard', absolute: false));
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-2">
        <!-- Email Address -->
        <flux:input wire:model="email" :label="__('Email address')" type="email" required autofocus autocomplete="email"
            placeholder="email@example.com" />

        <!-- Password or Login Code -->
        <div class="relative">
            <flux:input wire:model="password" :label="__('Password')" type="password" required
                autocomplete="current-password" :placeholder="__('Enter your password...')" viewable />

            @error('password')
                <div class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</div>
            @enderror

            <!-- Remember Me -->
             <div class="flex items-center justify-between mt-4 mb-2">
             <flux:checkbox wire:model="remember" :label="__('Remember me')" />

            @if (Route::has('password.request'))
                <flux:link class=" text-sm" :href="route('password.request')" wire:navigate>
                    {{ __('Forgot your password?') }}
                </flux:link>
            @endif
             </div>
            
        </div>



        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white">
                {{ __('Log in') }}
            </flux:button>
        </div>
    </form>

    @if (Route::has('register'))
        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            {{ __('Don\'t have an account?') }}
            <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
        </div>
    @endif
</div>
