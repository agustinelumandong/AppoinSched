<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use function Livewire\Volt\{rules};

rules([
    'username' => 'required|string|min:3|unique:' . User::class,
    'phone' => 'required|regex:/^(\+63|0)[\d]{10}$/',
    'email' => 'required|email|lowercase|string|unique:' . User::class,
    'password' => 'required|string|confirmed|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
])
    ->messages([
        'email.required' => 'The :attribute may not be empty.',
        'email.email' => 'The :attribute format is invalid.',
    ])
    ->attributes([
        'email' => 'email address',
    ]);

new #[Layout('components.layouts.auth')] class extends Component {
    public User $users;
    public string $username = '';
    public string $phone = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(User $users)
    {
        $this->users = $users;
    }

    protected function rules()
    {
        return [
            'username' => ['required', 'string', 'max:255', 'unique:' . User::class],
            'phone' => ['required', 'regex:/^(\+63|0)[\d]{10}$/', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/'],
        ];
    }

    protected function messages()
    {
        return [
            'email.required' => 'buang butangii ang input!!!.',
            'email.email' => 'The Email Address format is not valid.',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'email' => 'email address',
        ];
    }

    public function updated($prop)
    {
        $this->validateOnly($prop, $this->rules);
    }

    // public function rules(): array
    // {
    //     return [
    //         'username' => ['required', 'string', 'max:255', 'unique:' . User::class],
    //         'phone' => ['required', 'regex:/^(\+63|0)[\d]{10}$/', 'max:255'],
    //         'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
    //         'password' => ['required', 'string', 'confirmed', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/'],
    //     ];
    // }
    // +63912345678
    // 09123456789
    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate();
        dd($validated);
        $this->password = Hash::make($this->password);

        event(new Registered(($user = User::create($validated))));

        $user->assignRole('client');

        Auth::login($user);

        $this->redirectIntended(route('verification.notice', absolute: false));
        // $this->redirectIntended(route('dashboard', absolute: false));
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit.prevent="register" class="flex flex-col gap-1">
        <!-- Username -->
        <flux:input wire:model="username" :label="__('Username')" type="text" autocomplete="username"
            :placeholder="__('Username')" />


        <flux:input wire:model="phone" :label="__('Phone')" type="text" autocomplete="phone"
            :placeholder="__('+63 921 231 1234')" />

        <!-- Email Address -->
        <flux:input wire:model="email" :label="__('Email address')" type="email" autocomplete="email"
            placeholder="email@example.com" />

        <!-- Password -->
        <flux:input wire:model="password" :label="__('Password')" type="password" autocomplete="new-password"
            :placeholder="__('Password')" viewable />

        <!-- Confirm Password -->
        <flux:input wire:model="password_confirmation" :label="__('Confirm password')" type="password"
            autocomplete="new-password" :placeholder="__('Confirm password')" viewable />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full bg-blue-600 hover:bg-blue-700 text-white">
                {{ __('Create account') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Already have an account?') }}
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>
