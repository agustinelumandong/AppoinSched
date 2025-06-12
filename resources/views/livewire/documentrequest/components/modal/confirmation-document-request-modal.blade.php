<x-modal id="confirm-document-request" title="Confirm Document Request" size="max-w-2xl">
    <div class="modal-body">
        <p class="mt-3">
            Are you sure you want to {{ $confirmApproved ? 'approve' : ($confirmRejected ? 'reject' : 'set to pending') }} this document request?
        </p>
    </div>

    <x-slot name="footer">
        <div class="gap-2">
            <button type="button" class="btn btn-outline-secondary" x-data
                x-on:click="$dispatch('close-modal-confirm-document-request'); $wire.resetConfirmationStates()">
                <i class="bi bi-x-lg me-1"></i>Cancel
            </button>
            <button type="button" 
                class="btn {{ $confirmApproved ? 'btn-success' : ($confirmRejected ? 'btn-danger' : 'btn-warning') }}"
                wire:click="confirmDocumentRequest"
                wire:loading.attr="disabled"
                x-on:click="$dispatch('close-modal-confirm-document-request')">
                <span wire:loading.remove>
                    <i class="bi {{ $confirmApproved ? 'bi-check-circle' : ($confirmRejected ? 'bi-x-circle' : 'bi-clock') }} me-1"></i>
                    {{ $confirmApproved ? 'Confirm' : ($confirmRejected ? 'Reject' : 'Set Pending') }}
                </span>
                <span wire:loading>
                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                    Loading...
                </span>
            </button>
        </div>
    </x-slot>
</x-modal>
