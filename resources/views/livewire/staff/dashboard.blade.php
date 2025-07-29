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
                ->hasAnyRole(['MCR-staff', 'MTO-staff', 'BPLS-staff', 'admin', 'super-admin'])
        ) {
            abort(403, 'Unauthorized to access staff dashboard');
        }
    }

    public function with(): array
    {
        return [
            'recentAppointments' => Appointments::with(['user', 'office'])
                ->where('office_id', $this->getOfficeIdForStaff())
                ->latest()
                ->get(),
            'approvedAppointments' => Appointments::with(['user', 'office'])
                ->where('office_id', $this->getOfficeIdForStaff())
                ->latest()
                ->get(),
            'pendingAppointments' => Appointments::with(['user', 'office'])
                ->where('office_id', $this->getOfficeIdForStaff())
                ->where('status', 'pending')
                ->get(),
            'recentDocumentRequests' => DocumentRequest::with(['user', 'office', 'service'])
                ->where('office_id', $this->getOfficeIdForStaff())
                ->latest()
                ->take(5)
                ->get(),
            'pendingDocumentRequests' => DocumentRequest::with(['user', 'office', 'service'])
                ->where('office_id', $this->getOfficeIdForStaff())
                ->where('status', 'pending')
                ->get(),
            'approvedDocumentRequests' => DocumentRequest::with(['user', 'office', 'service'])
                ->where('office_id', $this->getOfficeIdForStaff())
                ->where('status', 'approved')
                ->get(),
            'rejectedDocumentRequests' => DocumentRequest::with(['user', 'office', 'service'])
                ->where('office_id', $this->getOfficeIdForStaff())
                ->where('status', 'rejected')
                ->get(),
        ];
    }
    public function getOfficeIdForStaff(): ?int
    {
        if (auth()->user()->hasRole('MCR-staff')) {
            return Offices::where('slug', 'municipal-civil-registrar')->value('id');
        }
        if (auth()->user()->hasRole('MTO-staff')) {
            return Offices::where('slug', 'municipal-treasurers-office')->value('id');
        }
        if (auth()->user()->hasRole('BPLS-staff')) {
            return Offices::where('slug', 'business-permits-and-licensing-section')->value('id');
        }
        return null;
    }
}; ?>
<div title="Staff Dashboard">
    @include('components.alert')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Staff Dashboard</h1>
                <p class="text-gray-600">Welcome back, {{ auth()->user()->first_name }}!</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="badge badge-secondary">{{ auth()->user()->roles->first()->name ?? 'No Role' }}</span>
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
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Appointments Calendar</h1>
                <p class="text-gray-600 mt-2">View and filter appointments across different offices and staff</p>
            </div>
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