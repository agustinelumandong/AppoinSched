<x-modal id="edit-appointment" title="Edit Appointment" size="max-w-2xl">
    <form wire:submit.prevent="updateAppointment" enctype="multipart/form-data">
        <div class="modal-body">
            <div class="mb-3">
                <label for="booking_date" class="form-label fw-semibold">Booking Date</label>
                <input type="text" class="form-control @error('booking_date') is-invalid @enderror" id="booking_date"
                    wire:model.defer="booking_date" placeholder="Enter booking date" required>
                @error('booking_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="booking_time" class="form-label fw-semibold">Booking Time</label>
                <input type="text" class="form-control @error('booking_time') is-invalid @enderror" id="booking_time"
                    wire:model.defer="booking_time" placeholder="Enter booking time" required>
                @error('booking_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label fw-semibold">Notes</label>
                {{-- <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" rows="3"
                    wire:model.defer="notes" placeholder="Enter notes"></textarea> --}}
                    <input type="text" class="form-control @error('notes') is-invalid @enderror" id="notes"
                    wire:model.defer="notes" placeholder="Enter notes">
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="status" class="form-label fw-semibold">Status</label>
                <select class="form-control @error('status') is-invalid @enderror form-select" id="status" wire:model.defer="status">
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="completed">Completed</option>
                    <option value="no-show">No Show</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </form>

        <x-slot name="footer">
            <div class="gap-2 ">
                <button type="button" class="btn btn-outline-secondary" x-data
                    x-on:click="$dispatch('close-modal-edit-appointment')">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>

                <button type="button" class="btn btn-primary ml-2" wire:loading.attr="disabled" wire:click="updateAppointment">
                    <span wire:loading.remove>
                        <i class="bi bi-save me-1"></i>
                        Save
                    </span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                        Loading...
                    </span>
                </button>
            </div>
        </x-slot>
</x-modal>
