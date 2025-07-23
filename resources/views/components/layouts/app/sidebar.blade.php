<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">

    <!-- Mobile Header -->
    <flux:header class="lg:hidden border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-3" inset="left" />

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

    <!-- Desktop Header -->
    <flux:header sticky class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <a href="{{ route('dashboard') }}"
            class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0 text-decoration-none text-black"
            wire:navigate>
            <x-app-logo />
        </a>



        <flux:spacer />

        <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
            <!-- Notification Bell -->
            <div class="px-4 py-2">
                <livewire:notification-bell />
            </div>
        </flux:navbar>

        <!-- Desktop User Menu -->
        <flux:dropdown position="top" align="end">
            <flux:profile class="cursor-pointer" :initials="auth()->user()->initials()" />

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
                        class="text-decoration-none text-black">{{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full text-decoration-none text-black">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    <!-- Desktop Sidebar -->
    <flux:sidebar sticky
        class="max-lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />



        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Platform')" class="grid">
                <flux:navlist.item icon="home" :href="route('dashboard')"
                    class="text-decoration-none text-black truncate" :current="request()->routeIs('dashboard')"
                    wire:navigate>{{ Str::limit(__('Dashboard'), 14) }}
                </flux:navlist.item>
            </flux:navlist.group>

            {{-- Client Navigation --}}
            @hasrole('client')
            <flux:navlist.group expandable :expanded="request()->routeIs('client.*')" heading="My Services">
                <flux:navlist.item icon="calendar" :href="route('client.appointments')"
                    :current="request()->routeIs('client.appointments')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('My Appointments'), 14) }}
                </flux:navlist.item>
                <flux:navlist.item icon="document-text" :href="route('client.documents')"
                    :current="request()->routeIs('client.documents')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('My Documents'), 14) }}
                </flux:navlist.item>
            </flux:navlist.group>
            @endhasrole

            {{-- Staff Navigation --}}
            @hasrole('MCR-staff|MTO-staff|BPLS-staff')
            <flux:navlist.group expandable :expanded="request()->routeIs('staff.*')" heading="Staff Management">
                <flux:navlist.item icon="calendar-days" :href="route('staff.appointments')"
                    :current="request()->routeIs('staff.appointments')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('Manage Appointments'), 14) }}
                </flux:navlist.item>
                <flux:navlist.item icon="document-duplicate" :href="route('staff.documents')"
                    :current="request()->routeIs('staff.documents')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('Process Documents'), 14) }}
                </flux:navlist.item>
                <flux:navlist.item icon="cog-6-tooth" :href="route('staff.services')"
                    :current="request()->routeIs('staff.services')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('Services Management'), 14) }}
                </flux:navlist.item>
            </flux:navlist.group>

            @endhasrole

            {{-- Admin Navigation --}}
            @hasrole('admin|super-admin')
            <flux:navlist.group expandable :expanded="request()->routeIs('admin.*')" heading="Administration">
                <flux:navlist.item icon="building-office-2" :href="route('admin.offices')"
                    :current="request()->routeIs('admin.offices')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('Offices Management'), 14) }}
                </flux:navlist.item>
                <flux:navlist.item icon="cog-6-tooth" :href="route('admin.services')"
                    :current="request()->routeIs('admin.services')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('Services Management'), 14) }}
                </flux:navlist.item>
                <flux:navlist.item icon="calendar" :href="route('admin.appointments-management')"
                    :current="request()->routeIs('admin.appointments-management')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('Appointments Management'), 14) }}
                </flux:navlist.item>
                <flux:navlist.item icon="document-text" :href="route('admin.document-request')"
                    :current="request()->routeIs('admin.document-request')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('Document Request Management'), 14) }}
                </flux:navlist.item>
                <flux:navlist.item icon="user" :href="route('admin.users.users-management')"
                    :current="request()->routeIs('admin.users.users-management')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('Users Management'), 14) }}
                </flux:navlist.item>
            </flux:navlist.group>
            @endhasrole

            {{-- Super Admin Navigation --}}
            @hasrole('super-admin')
            <flux:navlist.group expandable :expanded="request()->routeIs('admin.roles-permissions')" heading="System">
                <flux:navlist.item icon="shield-check" :href="route('admin.roles-permissions')"
                    :current="request()->routeIs('admin.roles-permissions')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('Roles & Permissions'), 14) }}
                </flux:navlist.item>
            </flux:navlist.group>
            @endhasrole
        </flux:navlist>

        <flux:spacer />

        @hasrole('client')
        <flux:navlist variant="outline">
            <flux:navlist.item icon="user" :href="route('userinfo')" wire:navigate
                class="text-decoration-none text-black truncate">
                {{ Str::limit(__('User Information'), 14) }}
            </flux:navlist.item>
        </flux:navlist>
        @endhasrole
        <!-- Desktop User Menu -->
        {{-- <flux:dropdown position="bottom" align="start">
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
        </flux:dropdown> --}}
    </flux:sidebar>

    <!-- Mobile Sidebar -->
    <flux:sidebar sticky stashable z-index-mobile="50"
        class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}"
            class="me-5 flex items-center space-x-2 rtl:space-x-reverse text-decoration-none text-black" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Platform')" class="grid">
                <flux:navlist.item icon="home" :href="route('dashboard')"
                    class="text-decoration-none text-black truncate" :current="request()->routeIs('dashboard')"
                    wire:navigate>{{ Str::limit(__('Dashboard'), 14) }}
                </flux:navlist.item>
            </flux:navlist.group>

            {{-- Client Navigation --}}
            @hasrole('client')
            <flux:navlist.group expandable :expanded="request()->routeIs('client.*')" heading="My Services">
                <flux:navlist.item icon="calendar" :href="route('client.appointments')"
                    :current="request()->routeIs('client.appointments')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('My Appointments'), 14) }}
                </flux:navlist.item>
                <flux:navlist.item icon="document-text" :href="route('client.documents')"
                    :current="request()->routeIs('client.documents')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('My Documents'), 14) }}
                </flux:navlist.item>
            </flux:navlist.group>
            @endhasrole

            {{-- Staff Navigation --}}
            @hasrole('MCR-staff|MTO-staff|BPLS-staff')
            <flux:navlist.group expandable :expanded="request()->routeIs('staff.*')" heading="Staff Management">
                <flux:navlist.item icon="calendar-days" :href="route('staff.appointments')"
                    :current="request()->routeIs('staff.appointments')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('Manage Appointments'), 14) }}
                </flux:navlist.item>
                <flux:navlist.item icon="document-duplicate" :href="route('staff.documents')"
                    :current="request()->routeIs('staff.documents')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('Process Documents'), 14) }}
                </flux:navlist.item>
            </flux:navlist.group>
            @endhasrole

            {{-- Admin Navigation --}}
            @hasrole('admin|super-admin')
            <flux:navlist.group expandable :expanded="request()->routeIs('admin.*')" heading="Administration">
                <flux:navlist.item icon="chart-bar" :href="route('admin.dashboard')"
                    :current="request()->routeIs('admin.dashboard')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('Admin Dashboard'), 14) }}
                </flux:navlist.item>
                <flux:navlist.item icon="building-office-2" :href="route('admin.offices')"
                    :current="request()->routeIs('admin.offices')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('Offices Management'), 14) }}
                </flux:navlist.item>
                <flux:navlist.item icon="cog-6-tooth" :href="route('admin.services')"
                    :current="request()->routeIs('admin.services')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('Services Management'), 14) }}
                </flux:navlist.item>
                <flux:navlist.item icon="calendar" :href="route('admin.appointments-management')"
                    :current="request()->routeIs('admin.appointments-management')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('Appointments Management'), 14) }}
                </flux:navlist.item>
                <flux:navlist.item icon="document-text" :href="route('admin.document-request')"
                    :current="request()->routeIs('admin.document-request')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('Document Request Management'), 14) }}
                </flux:navlist.item>
                <flux:navlist.item icon="user" :href="route('admin.users.users-management')"
                    :current="request()->routeIs('admin.users.users-management')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('Users Management'), 14) }}
                </flux:navlist.item>
            </flux:navlist.group>
            @endhasrole

            {{-- Super Admin Navigation --}}
            @hasrole('super-admin')
            <flux:navlist.group expandable :expanded="request()->routeIs('admin.roles-permissions')" heading="System">
                <flux:navlist.item icon="shield-check" :href="route('admin.roles-permissions')"
                    :current="request()->routeIs('admin.roles-permissions')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('Roles & Permissions'), 14) }}
                </flux:navlist.item>
            </flux:navlist.group>
            @endhasrole
        </flux:navlist>

        <flux:spacer />

        <flux:navlist variant="outline">
            <flux:navlist.item icon="user" :href="route('userinfo')" wire:navigate
                class="text-decoration-none text-black truncate">
                {{ Str::limit(__('User Information'), 14) }}
            </flux:navlist.item>
        </flux:navlist>
    </flux:sidebar>




    {{ $slot }}
    @livewireScripts
    @fluxScripts
    @stack('scripts')
</body>

</html>