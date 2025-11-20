<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

<head>
    @include('partials.head')
    <style>
        .d-header{
            background: linear-gradient(269deg, rgba(196, 213, 255, 0.5) 0%, #fff 100%);
        }
        .d-sidebar{
            background: linear-gradient(0deg, rgba(196, 213, 255, 0.5) 0%, #fff 5%);
        }
    </style>
</head>

<body class="min-h-screen bg-zinc-50">

    <!-- Mobile Header -->
    <flux:header class="lg:hidden border-b border-zinc-200 bg-white">
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
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black ">
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

                <form method="POST" action="{{ route('logout') }}" class="w-full" id="logout-form-mobile">
                    @csrf
                    <flux:menu.item as="button" type="button" icon="arrow-right-start-on-rectangle" class="w-full"
                        x-on:click="$dispatch('open-modal-logout-confirmation')">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    <!-- Desktop Header -->
    <flux:header sticky class="d-header border-b border-zinc-200 bg-white p-3">
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
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black ">
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

                <form method="POST" action="{{ route('logout') }}" class="w-full" id="logout-form-desktop">
                    @csrf
                    <flux:menu.item as="button" type="button" icon="arrow-right-start-on-rectangle"
                        class="w-full text-decoration-none text-black"
                        x-on:click="$dispatch('open-modal-logout-confirmation')">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    <!-- Desktop Sidebar -->
    <flux:sidebar sticky class="d-sidebar max-lg:hidden border-e border-zinc-200 bg-white ">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />



        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Platform')" class="grid">
                <flux:navlist.item
                    icon="home"
                    :href="route(auth()->user()->hasRole('super-admin|admin') ? 'admin.dashboard' : (auth()->user()->hasAnyRole('MCR-staff|MTO-staff|BPLS-staff|MCR-admin|MTO-admin|BPLS-admin') ? 'staff.dashboard' : 'client.dashboard'))"
                    :current="request()->routeIs('*.dashboard')"
                    wire:navigate
                    class="text-decoration-none text-black truncate h-12! w-full! mb-2!"
                >
                    {{ Str::limit(__('Dashboard'), 14) }}
                </flux:navlist.item>

            </flux:navlist.group>

            {{-- Client Navigation --}}
            @hasrole('client')
            {{-- request()->routeIs('client.*'); --}}
            <flux:navlist.group expandable heading="My Services">
                <flux:navlist.item icon="calendar" :href="route('client.appointments')"
                    :current="request()->routeIs('client.appointments')" wire:navigate
                    class="text-decoration-none text-black truncate h-10! w-full! mb-2!">
                    {{ Str::limit(__('My Appointments'), 14) }}
                </flux:navlist.item>
                <flux:navlist.item icon="document-text" :href="route('client.documents')"
                    :current="request()->routeIs('client.documents')" wire:navigate
                    class="text-decoration-none text-black truncate h-10! w-full!">
                    {{ Str::limit(__('My Documents'), 14) }}
                </flux:navlist.item>
            </flux:navlist.group>
            @endhasrole

            {{-- Staff Navigation --}}
            @hasrole('MCR-staff|MTO-staff|BPLS-staff|MCR-admin|MTO-admin|BPLS-admin')
            @php
                // Calculate new appointments and document requests for staff's assigned office
                $user = auth()->user();
                $officeId = $user->getOfficeIdForStaff();

                // Appointments query
                $appointmentsQuery = \App\Models\Appointments::query();
                if (!$user->hasRole('super-admin') && $officeId) {
                    $appointmentsQuery->where('office_id', $officeId);
                }
                $lastViewedAppointmentsAt = $user->last_viewed_appointments_at;
                $newAppointmentsCount = $lastViewedAppointmentsAt
                    ? $appointmentsQuery->where('created_at', '>', $lastViewedAppointmentsAt)->count()
                    : $appointmentsQuery->count();

                // Document requests query
                $docRequestsQuery = \App\Models\DocumentRequest::query();
                if (!$user->hasRole('super-admin') && $officeId) {
                    $docRequestsQuery->where('office_id', $officeId);
                }
                $lastViewedDocRequestsAt = $user->last_viewed_document_requests_at;
                $newDocRequestsCount = $lastViewedDocRequestsAt
                    ? $docRequestsQuery->where('created_at', '>', $lastViewedDocRequestsAt)->count()
                    : $docRequestsQuery->count();
            @endphp
            <flux:navlist.group expandable heading="Staff Management">
                <div class="relative">
                    <flux:navlist.item icon="calendar-days" :href="route('staff.appointments')"
                        :current="request()->routeIs('staff.appointments')" wire:navigate
                        class="text-decoration-none text-black truncate">
                        {{ Str::limit(__('Manage Appointments'), 14) }}
                    </flux:navlist.item>
                    @if($newAppointmentsCount > 0 && !request()->routeIs('staff.appointments'))
                        <span
                            class="absolute top-0 right-0 -mt-1 -mr-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-bold text-white bg-red-500 rounded-full z-10 pointer-events-none">
                            {{ $newAppointmentsCount > 99 ? '99+' : $newAppointmentsCount }}
                        </span>
                    @endif
                </div>
                <div class="relative">
                    <flux:navlist.item icon="document-duplicate" :href="route('staff.documents')"
                        :current="request()->routeIs('staff.documents')" wire:navigate
                        class="text-decoration-none text-black truncate">
                        {{ Str::limit(__('Process Documents'), 14) }}
                    </flux:navlist.item>
                    @if($newDocRequestsCount > 0 && !request()->routeIs('staff.documents'))
                        <span
                            class="absolute top-0 right-0 -mt-1 -mr-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-bold text-white bg-red-500 rounded-full z-10 pointer-events-none">
                            {{ $newDocRequestsCount > 99 ? '99+' : $newDocRequestsCount }}
                        </span>
                    @endif
                </div>
                <flux:navlist.item icon="cog-6-tooth" :href="route('staff.services')"
                    :current="request()->routeIs('staff.services')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('Services Management'), 14) }}
                </flux:navlist.item>
                <flux:navlist.item icon="chart-bar" :href="route('staff.reports')"
                    :current="request()->routeIs('staff.reports')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('Staff Reports'), 14) }}
                </flux:navlist.item>
            </flux:navlist.group>

            @endhasrole

            {{-- Admin Navigation --}}
            @hasrole('admin|super-admin')
            <flux:navlist.group expandable heading="Administration">
                <flux:navlist.item icon="building-office-2" :href="route('admin.offices')"
                    :current="request()->routeIs('admin.offices')" wire:navigate
                    class="text-decoration-none text-black truncate h-10! w-full! mb-2!">
                    {{ Str::limit(__('Offices Management'), 14) }}
                </flux:navlist.item>
                <flux:navlist.item icon="cog-6-tooth" :href="route('admin.services')"
                    :current="request()->routeIs('admin.services')" wire:navigate
                    class="text-decoration-none text-black truncate h-10! w-full! mb-2!">
                    {{ Str::limit(__('Services Management'), 14) }}
                </flux:navlist.item>
                @php
                    // Calculate new appointments count for admin
                    $lastViewedAppointmentsAt = auth()->user()->last_viewed_appointments_at;
                    $newAppointmentsCount = $lastViewedAppointmentsAt
                        ? \App\Models\Appointments::where('created_at', '>', $lastViewedAppointmentsAt)->count()
                        : \App\Models\Appointments::count();
                @endphp
                <div class="relative">
                    <flux:navlist.item icon="calendar" :href="route('admin.appointments-management')"
                        :current="request()->routeIs('admin.appointments-management')" wire:navigate
                        class="text-decoration-none text-black truncate h-10! w-full! mb-2!">
                        {{ Str::limit(__('Appointments Management'), 14) }}
                    </flux:navlist.item>
                    @if($newAppointmentsCount > 0 && !request()->routeIs('admin.appointments-management'))
                        <span
                            class="absolute top-0 right-0 -mt-1 -mr-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-bold text-white bg-red-500 rounded-full z-10 pointer-events-none">
                            {{ $newAppointmentsCount > 99 ? '99+' : $newAppointmentsCount }}
                        </span>
                    @endif
                </div>
                @php
                    // Calculate new document requests count for admin
                    $lastViewedDocRequestsAt = auth()->user()->last_viewed_document_requests_at;
                    $newDocRequestsCount = $lastViewedDocRequestsAt
                        ? \App\Models\DocumentRequest::where('created_at', '>', $lastViewedDocRequestsAt)->count()
                        : \App\Models\DocumentRequest::count();
                @endphp
                <div class="relative">
                    <flux:navlist.item icon="document-text" :href="route('admin.document-request')"
                        :current="request()->routeIs('admin.document-request')" wire:navigate
                        class="text-decoration-none text-black truncate h-10! w-full! mb-2!">
                        {{ Str::limit(__('Document Request Management'), 14) }}
                    </flux:navlist.item>
                    @if($newDocRequestsCount > 0 && !request()->routeIs('admin.document-request'))
                        <span
                            class="absolute top-0 right-0 -mt-1 -mr-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-bold text-white bg-red-500 rounded-full z-10 pointer-events-none">
                            {{ $newDocRequestsCount > 99 ? '99+' : $newDocRequestsCount }}
                        </span>
                    @endif
                </div>
                @php
                    // Calculate new users count (users created since last visit)
                    $lastViewedAt = auth()->user()->last_viewed_users_at;
                    $newUsersCount = $lastViewedAt
                        ? \App\Models\User::where('created_at', '>', $lastViewedAt)->count()
                        : \App\Models\User::count();
                @endphp
                <div class="relative">
                    <flux:navlist.item icon="user" :href="route('admin.users.users-management')"
                        :current="request()->routeIs('admin.users.users-management')" wire:navigate
                        class="text-decoration-none text-black truncate h-10! w-full! mb-2!">
                        {{ Str::limit(__('Users Management'), 14) }}
                    </flux:navlist.item>
                    @if($newUsersCount > 0 && !request()->routeIs('admin.users.users-management'))
                        <span
                            class="absolute top-0 right-0 -mt-1 -mr-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-bold text-white bg-red-500 rounded-full z-10 pointer-events-none">
                            {{ $newUsersCount > 99 ? '99+' : $newUsersCount }}
                        </span>
                    @endif
                </div>
            </flux:navlist.group>
            @endhasrole

            {{-- Super Admin Navigation --}}
            @hasrole('super-admin')
            <flux:navlist.group expandable heading="System">
                <flux:navlist.item icon="shield-check" :href="route('admin.roles-permissions')"
                    :current="request()->routeIs('admin.roles-permissions')" wire:navigate
                    class="text-decoration-none text-black truncate h-10! w-full! mb-2!">
                    {{ Str::limit(__('Roles & Permissions'), 14) }}
                </flux:navlist.item>
            </flux:navlist.group>
            @endhasrole
        </flux:navlist>

        <flux:spacer />

        @hasrole('client')
        <flux:navlist variant="outline">
            <flux:navlist.item
                icon="user"
                :href="route('userinfo')"
                :current="request()->routeIs('userinfo')"
                wire:navigate
                class="text-decoration-none text-black truncate h-10! w-full! mb-2!">
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
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black">
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
        class="lg:hidden border-e border-zinc-200 bg-white">
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
            @hasrole('MCR-staff|MTO-staff|BPLS-staff|MCR-admin|MTO-admin|BPLS-admin')
            @php
                // Calculate new appointments and document requests for staff's assigned office
                $user = auth()->user();
                $officeId = $user->getOfficeIdForStaff();

                // Appointments query
                $appointmentsQuery = \App\Models\Appointments::query();
                if (!$user->hasRole('super-admin') && $officeId) {
                    $appointmentsQuery->where('office_id', $officeId);
                }
                $lastViewedAppointmentsAt = $user->last_viewed_appointments_at;
                $newAppointmentsCount = $lastViewedAppointmentsAt
                    ? $appointmentsQuery->where('created_at', '>', $lastViewedAppointmentsAt)->count()
                    : $appointmentsQuery->count();

                // Document requests query
                $docRequestsQuery = \App\Models\DocumentRequest::query();
                if (!$user->hasRole('super-admin') && $officeId) {
                    $docRequestsQuery->where('office_id', $officeId);
                }
                $lastViewedDocRequestsAt = $user->last_viewed_document_requests_at;
                $newDocRequestsCount = $lastViewedDocRequestsAt
                    ? $docRequestsQuery->where('created_at', '>', $lastViewedDocRequestsAt)->count()
                    : $docRequestsQuery->count();
            @endphp
            <flux:navlist.group expandable heading="Staff Management">
                <div class="relative">
                    <flux:navlist.item icon="calendar-days" :href="route('staff.appointments')"
                        :current="request()->routeIs('staff.appointments')" wire:navigate
                        class="text-decoration-none text-black truncate">
                        {{ Str::limit(__('Manage Appointments'), 14) }}
                    </flux:navlist.item>
                    @if($newAppointmentsCount > 0 && !request()->routeIs('staff.appointments'))
                        <span
                            class="absolute top-0 right-0 -mt-1 -mr-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-bold text-white bg-red-500 rounded-full z-10 pointer-events-none">
                            {{ $newAppointmentsCount > 99 ? '99+' : $newAppointmentsCount }}
                        </span>
                    @endif
                </div>
                <div class="relative">
                    <flux:navlist.item icon="document-duplicate" :href="route('staff.documents')"
                        :current="request()->routeIs('staff.documents')" wire:navigate
                        class="text-decoration-none text-black truncate">
                        {{ Str::limit(__('Process Documents'), 14) }}
                    </flux:navlist.item>
                    @if($newDocRequestsCount > 0 && !request()->routeIs('staff.documents'))
                        <span
                            class="absolute top-0 right-0 -mt-1 -mr-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-bold text-white bg-red-500 rounded-full z-10 pointer-events-none">
                            {{ $newDocRequestsCount > 99 ? '99+' : $newDocRequestsCount }}
                        </span>
                    @endif
                </div>
                <flux:navlist.item icon="cog-6-tooth" :href="route('staff.services')"
                    :current="request()->routeIs('staff.services')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('Services Management'), 14) }}
                </flux:navlist.item>
                <flux:navlist.item icon="chart-bar" :href="route('staff.reports')"
                    :current="request()->routeIs('staff.reports')" wire:navigate
                    class="text-decoration-none text-black truncate">
                    {{ Str::limit(__('Staff Reports'), 14) }}
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
                @php
                    // Calculate new appointments count for admin
                    $lastViewedAppointmentsAt = auth()->user()->last_viewed_appointments_at;
                    $newAppointmentsCount = $lastViewedAppointmentsAt
                        ? \App\Models\Appointments::where('created_at', '>', $lastViewedAppointmentsAt)->count()
                        : \App\Models\Appointments::count();
                @endphp
                <div class="relative">
                    <flux:navlist.item icon="calendar" :href="route('admin.appointments-management')"
                        :current="request()->routeIs('admin.appointments-management')" wire:navigate
                        class="text-decoration-none text-black truncate">
                        {{ Str::limit(__('Appointments Management'), 14) }}
                    </flux:navlist.item>
                    @if($newAppointmentsCount > 0 && !request()->routeIs('admin.appointments-management'))
                        <span
                            class="absolute top-0 right-0 -mt-1 -mr-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-bold text-white bg-red-500 rounded-full z-10 pointer-events-none">
                            {{ $newAppointmentsCount > 99 ? '99+' : $newAppointmentsCount }}
                        </span>
                    @endif
                </div>
                @php
                    // Calculate new document requests count for admin
                    $lastViewedDocRequestsAt = auth()->user()->last_viewed_document_requests_at;
                    $newDocRequestsCount = $lastViewedDocRequestsAt
                        ? \App\Models\DocumentRequest::where('created_at', '>', $lastViewedDocRequestsAt)->count()
                        : \App\Models\DocumentRequest::count();
                @endphp
                <div class="relative">
                    <flux:navlist.item icon="document-text" :href="route('admin.document-request')"
                        :current="request()->routeIs('admin.document-request')" wire:navigate
                        class="text-decoration-none text-black truncate">
                        {{ Str::limit(__('Document Request Management'), 14) }}
                    </flux:navlist.item>
                    @if($newDocRequestsCount > 0 && !request()->routeIs('admin.document-request'))
                        <span
                            class="absolute top-0 right-0 -mt-1 -mr-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-bold text-white bg-red-500 rounded-full z-10 pointer-events-none">
                            {{ $newDocRequestsCount > 99 ? '99+' : $newDocRequestsCount }}
                        </span>
                    @endif
                </div>
                @php
                    // Calculate new users count (users created since last visit)
                    $lastViewedAt = auth()->user()->last_viewed_users_at;
                    $newUsersCount = $lastViewedAt
                        ? \App\Models\User::where('created_at', '>', $lastViewedAt)->count()
                        : \App\Models\User::count();
                @endphp
                <div class="relative">
                    <flux:navlist.item icon="user" :href="route('admin.users.users-management')"
                        :current="request()->routeIs('admin.users.users-management')" wire:navigate
                        class="text-decoration-none text-black truncate">
                        {{ Str::limit(__('Users Management'), 14) }}
                    </flux:navlist.item>
                    @if($newUsersCount > 0 && !request()->routeIs('admin.users.users-management'))
                        <span
                            class="absolute top-0 right-0 -mt-1 -mr-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-bold text-white bg-red-500 rounded-full z-10 pointer-events-none">
                            {{ $newUsersCount > 99 ? '99+' : $newUsersCount }}
                        </span>
                    @endif
                </div>
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


    <!-- Logout Confirmation Modal -->
    <x-modal id="logout-confirmation" title="Confirm Logout" size="max-w-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="bi bi-exclamation-triangle text-yellow-500 text-2xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-lg text-gray-700">
                    Are you sure you want to log out?
                </p>
            </div>
        </div>

        <x-slot name="footer">
            <button type="button" class="btn-sm flux-btn flux-btn-outline" x-data
                x-on:click="$dispatch('close-modal-logout-confirmation')" style="margin-right:12px">
                Cancel
            </button>
            <button type="button" class="btn-sm flux-btn flux-btn-danger" x-data
                x-on:click="$dispatch('close-modal-logout-confirmation'); (document.getElementById('logout-form-mobile') || document.getElementById('logout-form-desktop')).submit()">
                Logout
            </button>
        </x-slot>
    </x-modal>
</body>

</html>
