<div>
    <div class="flux-card mb-4" style="padding: 12px;">
        <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
            <h5 class="mb-0 fw-semibold">Appointments</h5>
            <div class="d-flex align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <input type="text" class="flux-form-control search" placeholder="Search appointments"
                        wire:model.live="search" wire:keyup="searchs" wire:debounce.300ms>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table flux-table mb-0">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Office</th>
                        <th>Notes</th>
                        <th>Date&Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appointment)
                        <tr>
                            <td>
                                <p class="fw-semibold">
                                    {{ $appointment->user?->first_name . ' ' . $appointment->user?->last_name ?? 'N/A' }}
                            </td>

                            <td>
                                <p class="fw-semibold ">{{ $appointment->office?->name ?? 'N/A' }}</p>
                            </td>
                            <td>
                                <p class="fw-semibold">{{ $appointment->notes ?? 'N/A' }}</p>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($appointment->booking_date)->format('M d, Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($appointment->booking_time)->format('h:i A') }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="flux-btn flux-btn-primary btn-sm"
                                        wire:click="openShowAppointmentModal({{ $appointment->id }})" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    {{-- <button class="flux-btn flux-btn-success btn-sm"
                                        wire:click="openEditAppointmentModal({{ $appointment->id }})" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button> --}}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-person-badge display-4 mb-3"></i>
                                    <div>No appointments found. Create your first appointment to get started.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination links -->
        <div class="mt-3">
            {{ $appointments->links(data: ['scrollTo' => false]) }}
        </div>
    </div>

</div>