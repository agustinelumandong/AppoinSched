<x-modal id="confirm-modal" title="Confirm Cancel Appointment" size="max-w-2xl">
    <div class="modal-body">
        <p class="mt-3">
            Are you sure you want to cancel the {{ $this->appointment->reference_number ?? 'this' }} appointment?
        </p>
    </div>

    <x-slot name="footer">
        <div class="gap-2">
            <button type="button" class="btn btn-outline-secondary" x-data
                x-on:click="$dispatch('close-modal-confirm-modal')">
                <i class="bi bi-x-lg me-1"></i>Cancel
            </button>
            <button type="button" class="btn btn-danger"
                wire:click="confirmCancelAppointment({{ $this->appointment->id ?? '' }})" wire:loading.attr="disabled"
                x-on:click="$dispatch('close-modal-confirm-modal')">
                <span wire:loading.remove>
                    <i class="bi bi-x-circle me-1"></i>
                    Confirm
                </span>
                <span wire:loading>
                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                    Loading...
                </span>
            </button>
        </div>
    </x-slot>
</x-modal>