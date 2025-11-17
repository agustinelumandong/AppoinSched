<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use App\Models\Appointments;
use App\Models\Offices;
use App\Models\DocumentRequest;

new class extends Component {
    public function mount()
    {
        if (
            !auth()
                ->user()
                ->hasAnyRole(['MCR-staff', 'MTO-staff', 'BPLS-staff', 'MCR-admin', 'MTO-admin', 'BPLS-admin', 'admin', 'super-admin'])
        ) {
            abort(403, 'Unauthorized to access staff dashboard');
        }
    }

    public function with(): array
    {
        $user = auth()->user();
        $officeId = $user->getOfficeIdForStaff();

        // Super-admin sees all offices, others see only their assigned office
        $appointmentsQuery = Appointments::with(['user', 'office']);
        $documentsQuery = DocumentRequest::with(['user', 'office', 'service']);

        if (!$user->hasRole('super-admin') && $officeId) {
            $appointmentsQuery->where('office_id', $officeId);
            $documentsQuery->where('office_id', $officeId);
        }

        return [
            'recentAppointments' => (clone $appointmentsQuery)
                ->latest()
                ->take(5)
                ->get(),
            'approvedAppointments' => (clone $appointmentsQuery)
                ->latest()
                ->get(),
            'pendingAppointments' => (clone $appointmentsQuery)
                ->where('status', 'pending')
                ->get(),
            'recentDocumentRequests' => (clone $documentsQuery)
                ->latest()
                ->take(5)
                ->get(),
            'pendingDocumentRequests' => (clone $documentsQuery)
                ->where('status', 'pending')
                ->get(),
            'approvedDocumentRequests' => (clone $documentsQuery)
                ->where('status', 'approved')
                ->get(),
            'rejectedDocumentRequests' => (clone $documentsQuery)
                ->where('status', 'rejected')
                ->get(),
        ];
    }
}; ?>
<div title="Staff Dashboard">
    @include('components.alert')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h4 class="text-3xl font-bold text-gray-900">Staff Dashboard</h4>
                <p class="text-gray-600">Welcome back, {{ auth()->user()->first_name }}!</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="flux-badge flux-badge-primary">{{ auth()->user()->roles->first()->name ?? 'No Role' }}</span>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                        <p class="text-sm font-medium text-gray-600">Recent Appointments</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $recentAppointments->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Today's Appointments</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $approvedAppointments->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Reviews</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $pendingAppointments->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                        <p class="text-sm font-medium text-gray-600">Pending Document Requests</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $pendingDocumentRequests->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Approved Document Requests</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $approvedDocumentRequests->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Rejected Document Requests</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $rejectedDocumentRequests->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="  py-8">
            {{-- <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Appointments Calendar</h1>
                <p class="text-gray-600 mt-2">View and filter appointments across different offices and staff</p>
            </div> --}}
            <livewire:components.full-calendar />
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 bg-blue-100">
                <h3 class="text-lg font-semibold text-gray-900">Recent Appointments</h3>
            </div>
            <div>
                <div class="overflow-x-auto">
                    <table class="flux-table w-full">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Service</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAppointments as $appointment)
                                                    <tr>
                                                        <td>
                                                            {{ $appointment->user?->first_name ?? 'N/A' }}
                                                            {{ $appointment->user?->last_name ?? '' }}
                                                        </td>
                                                        <td>
                                                            {{ $appointment->service?->title ?? 'N/A' }}
                                                        </td>
                                                        <td>
                                                            {{ \Illuminate\Support\Carbon::parse($appointment->booking_date)->format('M d, Y') }}
                                                            {{ \Illuminate\Support\Carbon::parse($appointment->booking_time)->format('h:i A') }}
                                                        </td>
                                                        <td>
                                                            <span class="flux-badge {{ $appointment->status === 'pending'
                                ? 'flux-badge-warning'
                                : ($appointment->status === 'approved'
                                    ? 'flux-badge-success'
                                    : ($appointment->status === 'cancelled'
                                        ? 'flux-badge-danger'
                                        : 'flux-badge-info')) }}">
                                                                {{ ucfirst($appointment->status) }}
                                                            </span>
                                                        </td>

                                                    </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-gray-500">No appointments found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 bg-green-100">
                <h3 class="text-lg font-semibold text-gray-900">Recent Document Requests</h3>
            </div>
            <div>
                <div class="overflow-x-auto">
                    <table class="flux-table w-full">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Service</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentDocumentRequests as $documentRequest)
                                                    <tr>
                                                        <td>
                                                            {{ $documentRequest->user?->first_name ?? 'N/A' }}
                                                            {{ $documentRequest->user?->last_name ?? '' }}
                                                        </td>
                                                        <td>
                                                            {{ $documentRequest->service?->title ?? 'N/A' }}
                                                        </td>
                                                        <td>
                                                            {{ \Illuminate\Support\Carbon::parse($documentRequest->booking_date)->format('M d, Y') }}
                                                            {{ \Illuminate\Support\Carbon::parse($documentRequest->booking_time)->format('h:i A') }}
                                                        </td>
                                                        <td>
                                                            <span class="flux-badge {{ $documentRequest->status === 'pending'
                                ? 'flux-badge-warning'
                                : ($documentRequest->status === 'approved'
                                    ? 'flux-badge-success'
                                    : ($documentRequest->status === 'cancelled'
                                        ? 'flux-badge-danger'
                                        : 'flux-badge-info')) }}">
                                                                {{ ucfirst($documentRequest->status) }}
                                                            </span>
                                                        </td>

                                                    </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-gray-500">No document requests found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>