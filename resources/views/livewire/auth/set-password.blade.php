<?php

declare(strict_types=1);

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(): void
    {
        if (!auth()->check()) {
            $this->redirect(route('login', absolute: false));
            return;
        }

        if (auth()->user()->hasPasswordSet()) {
            $this->redirect(route('dashboard', absolute: false));
            return;
        }
    }

    public function setPassword(): void
    {
        $this->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
            ],
            'password_confirmation' => 'required|string|same:password',
        ], [
            'password.regex' => 'Password must be at least 8 characters long and include both lowercase and uppercase letters, a number, and a special character.',
        ]);

        $user = Auth::user();

        if (!$user) {
            $this->redirect(route('login', absolute: false));
            return;
        }

        $user->password = Hash::make($this->password);
        $user->password_set_at = now();
        $user->save();

        // Clear login code
        $user->clearLoginCode();

        session()->flash('success', 'Password set successfully. You can now use your email and password to log in.');

        $this->redirect(route('dashboard', absolute: false));
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Set Your Password')" :description="__('Please set a secure password for your account')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit.prevent="setPassword" class="flex flex-col gap-6">
        <!-- Password -->
        <div>
            <flux:input wire:model="password" :label="__('New Password')" type="password" required
                autocomplete="new-password" :placeholder="__('Enter your password')" viewable />
            @error('password')
                <div class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password Confirmation -->
        <div>
            <flux:input wire:model="password_confirmation" :label="__('Confirm Password')" type="password" required
                autocomplete="new-password" :placeholder="__('Confirm your password')" viewable />
            @error('password_confirmation')
                <div class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password Requirements -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <p class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-2">Password Requirements:</p>
            <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1 list-disc list-inside">
                <li>At least 8 characters long</li>
                <li>Contains at least one lowercase letter</li>
                <li>Contains at least one uppercase letter</li>
                <li>Contains at least one number</li>
                <li>Contains at least one special character</li>
            </ul>
        </div>

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="setPassword">
                    {{ __('Set Password') }}
                </span>
                <span wire:loading wire:target="setPassword">
                    {{ __('Setting Password...') }}
                </span>
            </flux:button>
        </div>
    </form>
</div>

