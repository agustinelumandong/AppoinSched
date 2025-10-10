<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\Appointments;
use App\Models\DocumentRequest;

new class extends Component {
    public function mount()
    {
        if (
            !auth()
                ->user()
                ->hasAnyRole(['admin', 'super-admin'])
        ) {
            abort(403, 'Unauthorized to access admin dashboard');
        }
    }

    public function with(): array
    {
        return [
            'recentAppointments' => Appointments::with(['user', 'office'])
                ->latest()
                ->take(5)
                ->get(),
            'recentDocumentRequests' => DocumentRequest::with(['user', 'office', 'service'])
                ->latest()
                ->take(5)
                ->get(),
        ];
    }
}; ?>

<div>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex justify-between items-center">
            <div>
                <h4 class="text-3xl font-bold text-gray-900">Admin Dashboard</h4>
                <p class="text-xs text-gray-600">Welcome back, {{ auth()->user()->first_name }}!</p>
            </div>
            <div class="flex items-center space-x-2">
                <span
                    class="flux-badge flux-badge-primary">{{ auth()->user()->roles->first()->name ?? 'No Role' }}</span>
            </div>
        </div>
        {{-- Header --}}

        {{-- Recent Users and Appointments Cards --}}
        <div class="grid grid-cols-3 md:grid-cols-3 lg:grid-cols-3 gap-6">
            {{-- Total Users --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ User::count() }}</p>
                    </div>
                </div>
            </div>
            {{-- Total Document Requests --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Document Requests</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ DocumentRequest::count() }}</p>
                    </div>
                </div>
            </div>
            {{-- Total Document Requests Success/Complete/Ready for pickup --}}
            {{-- Total Document Requests Pending/On-going/In Progress --}}
            {{-- Total Document Requests Rejected/Failed --}}
            {{-- Total Document Requests Cancelled --}}
            {{-- Total Appointments --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Appointments</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ Appointments::count() }}</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Total Document Requests by Status & Payment --}}
        <div class="grid grid-cols-4 md:grid-cols-4 lg:grid-cols-4 gap-6 mt-4">

            {{-- Completed / Ready for Pickup --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Completed / Ready</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ new App\Models\DocumentRequest()->totalCompletedRequests() }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Pending / In Progress --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending / In Progress</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ new App\Models\DocumentRequest()->totalPendingRequests() }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Rejected / Failed --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Rejected / Failed</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ new App\Models\DocumentRequest()->totalRejectedRequests() }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Unpaid --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 1.343-3 3v1a3 3 0 003 3h0a3 3 0 003-3v-1a3 3 0 00-3-3zm0 9v1m0-13v1m-6 4h12">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Unpaid Requests</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ new App\Models\DocumentRequest()->totalUnpaidRequests() }}
                        </p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Payment Status --}}
        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-6 mt-4">

            {{-- Paid --}}
            {{-- <div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center">
      <div class="p-2 bg-blue-100 rounded-lg">
        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 8c-1.657 0-3 1.343-3 3v1a3 3 0 003 3h0a3 3 0 003-3v-1a3 3 0 00-3-3zm0 9v1m0-13v1m-6 4h12"></path>
        </svg>
      </div>
      <div class="ml-4">
        <p class="text-sm font-medium text-gray-600">Paid Requests</p>
        <p class="text-2xl font-semibold text-gray-900">
          {{ (new App\Models\DocumentRequest)->totalPaidRequests() }}
        </p>
      </div>
    </div>
  </div> --}}



        </div>

        {{-- Recent Users and Appointments List --}}
        <div class="flex flex-row w-full gap-6">
            {{-- Total Users List --}}
            <div class="bg-white rounded-lg shadow flex-1 min-w-0">
          <div class="p-6 border-b border-gray-200 bg-yellow-100">
              <h3 class="text-lg font-semibold text-gray-900">Recent Users</h3>
          </div>
          <div class="p-6">
              <div class="space-y-4">
            @forelse(User::latest()->take(5)->get() as $user)
                <div class="flex items-center justify-between">
              <div class="flex items-center">
                  <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                <span class="text-sm font-medium text-gray-700">
                    {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                </span>
                  </div>
                  <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">{{ $user->first_name }}
                    {{ $user->last_name }}</p>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                  </div>
              </div>
              <span class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <p class="text-gray-500 text-center">No users found</p>
            @endforelse
              </div>
          </div>
            </div>

            {{-- Recent Appointments List --}}
            <div class="bg-white rounded-lg shadow flex-1 min-w-0">
          <div class="p-6 border-b border-gray-200 bg-blue-100">
              <h3 class="text-lg font-semibold text-gray-900 ">Recent Appointments</h3>
          </div>
          <div class="p-6">
              <div class="space-y-4">
            @forelse($recentAppointments as $appointment)
                <div class="flex items-center justify-between">
              <div>
                  <p class="text-sm font-medium text-gray-900">{{ $appointment->user?->first_name ?? 'N/A' }}
                {{ $appointment->user?->last_name ?? '' }}
                  </p>
                  <p class="text-sm text-gray-500">{{ $appointment->office?->name ?? 'N/A' }} -
                {{ $appointment->service?->title ?? 'N/A' }}
                  </p>
              </div>
              <div class="text-right">
                  <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
          {{ $appointment->status === 'pending'
              ? 'bg-yellow-100 text-yellow-800'
              : ($appointment->status === 'approved'
            ? 'bg-green-100 text-green-800'
            : ($appointment->status === 'cancelled'
                ? 'bg-red-100 text-red-800'
                : ($appointment->status === 'completed'
              ? 'bg-green-100 text-green-800'
              : 'bg-gray-100 text-gray-800'))) }}">
                {{ ucfirst($appointment->status) }}
                  </span>
                  <p class="text-xs text-gray-500 mt-1">{{ $appointment->booking_date }}</p>
              </div>
                </div>
            @empty
                <p class="text-gray-500 text-center">No appointments found</p>
            @endforelse
              </div>
          </div>
            </div>

            {{-- Recent Document Request List --}}
            <div class="bg-white rounded-lg shadow flex-1 min-w-0">
          <div class="p-6 border-b border-gray-200 bg-green-100">
              <h3 class="text-lg font-semibold text-gray-900 ">Recent Document Requests</h3>
          </div>
          <div class="p-6">
              <div class="space-y-4">
            @forelse($recentDocumentRequests as $request)
                <div class="flex items-center justify-between">
              <div>
                  <p class="text-sm font-medium text-gray-900">{{ $request->user?->first_name ?? 'N/A' }}
                {{ $request->user?->last_name ?? '' }}
                  </p>
                  <p class="text-sm text-gray-500">{{ $request->office?->name ?? 'N/A' }} -
                {{ $request->service?->title ?? 'N/A' }}
                  </p>
              </div>
              <div class="text-right">
                  <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
          {{ $request->status === 'pending'
              ? 'bg-yellow-100 text-yellow-800'
              : ($request->status === 'approved'
            ? 'bg-green-100 text-green-800'
            : ($request->status === 'cancelled'
                ? 'bg-red-100 text-red-800'
                : ($request->status === 'completed'
              ? 'bg-green-100 text-green-800'
              : 'bg-gray-100 text-gray-800'))) }}">
                {{ ucfirst($request->status) }}
                  </span>
                  <p class="text-xs text-gray-500 mt-1">{{ $request->requested_date }}</p>
              </div>
                </div>
            @empty
                <p class="text-gray-500 text-center">No document requests found</p>
            @endforelse
              </div>
          </div>
            </div>
        </div>
    </div>
</div>
