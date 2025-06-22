<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 ">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}"
            class="me-5 flex items-center space-x-2 rtl:space-x-reverse text-decoration-none text-black" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Platform')" class="grid">
                <flux:navlist.item icon="home" :href="route('dashboard')" class="text-decoration-none text-black"
                    :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}
                </flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group :heading="__('Administration')" class="grid">
                <flux:navlist.item icon="building-office" :href="route('admin.offices')"
                    class="text-decoration-none text-black" :current="request()->routeIs('admin.offices')"
                    wire:navigate>{{ __('Offices') }}
                </flux:navlist.item>
                <flux:navlist.item icon="building-office" :href="route('admin.services')"
                    class="text-decoration-none text-black" :current="request()->routeIs('admin.services')"
                    wire:navigate>{{ __('Services') }}
                </flux:navlist.item>
                <flux:navlist.item icon="calendar" :href="route('admin.appointments-management')"
                    class="text-decoration-none text-black"
                    :current="request()->routeIs('admin.appointments-management')" wire:navigate>
                    {{ __('Appointments') }}
                </flux:navlist.item>
                <flux:navlist.item icon="document-text" :href="route('admin.document-request')"
                    class="text-decoration-none text-black" :current="request()->routeIs('admin.document-request')"
                    wire:navigate>{{ __('Document Request') }}
                </flux:navlist.item>
                <flux:navlist.item icon="user" :href="route('admin.users.users-management')"
                    class="text-decoration-none text-black"
                    :current="request()->routeIs('admin.users.users-management')" wire:navigate>
                    {{ __('Users Management') }}
                </flux:navlist.item>
                <flux:navlist.item icon="shield-check" :href="route('admin.roles-permissions')"
                    class="text-decoration-none text-black" :current="request()->routeIs('admin.roles-permissions')"
                    wire:navigate>{{ __('Roles & Permissions') }}
                </flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        <flux:navlist variant="outline">
            <flux:navlist.item icon="user" href="#" class="text-decoration-none text-black">
                {{ __('User Information') }}
            </flux:navlist.item>
        </flux:navlist>

        <!-- Desktop User Menu -->
        <flux:dropdown position="bottom" align="start">
            <flux:profile :name="auth()->user()->first_name . ' ' . auth()->user()->last_name"
                :initials="auth()->user()->initials()" icon-trailing="chevrons-up-down" />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate
                        class="text-decoration-none text-black">{{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate
                        class="text-decoration-none text-black">
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}
    @livewireScripts
    @fluxScripts
</body>

</html>