<x-modal id="confirm-document-request" title="Confirm Action" size="max-w-2xl">
    <div class="modal-body">
        <p class="mt-3">
            @if ($confirmPaymentStatus)
                Are you sure you want to update the payment status to
                <strong>{{ ucfirst($paymentStatusToUpdate) }}</strong>?
                @if ($paymentStatusToUpdate === 'paid')
                    <span class="block mt-2 text-sm text-blue-600">
                        Note: This will automatically update the document status to "In Progress".
                    </span>
                @elseif($paymentStatusToUpdate === 'failed' || $paymentStatusToUpdate === 'unpaid')
                    <span class="block mt-2 text-sm text-amber-600">
                        Note: This will set the document status to "Pending".
                    </span>
                @endif
            @else
                Are you sure you want to
                {{ $confirmApproved ? 'approve' : ($confirmRejected ? 'reject' : 'set to pending') }} this document
                request?
            @endif
        </p>

        @if (!empty($remarks))
            <div class="mt-4">
                <h4 class="text-sm font-medium text-gray-700">Remarks:</h4>
                <p class="text-sm text-gray-600 bg-gray-50 p-2 rounded mt-1">{{ $remarks }}</p>
            </div>
        @endif
    </div>

    <x-slot name="footer">
        <div class="gap-2">
            <button type="button" class="flux-btn flux-btn-outline" x-data="{}"
                x-on:click="$dispatch('close-modal-confirm-document-request'); $wire.resetConfirmationStates()">
                <i class="bi bi-x-lg me-1"></i>Cancel
            </button>
            <button type="button"
                class="flux-btn {{ $confirmPaymentStatus
                    ? ($paymentStatusToUpdate === 'paid'
                        ? 'flux-btn-success'
                        : ($paymentStatusToUpdate === 'failed'
                            ? 'flux-btn-danger'
                            : 'flux-btn-secondary'))
                    : ($confirmApproved
                        ? 'flux-btn-success'
                        : ($confirmRejected
                            ? 'flux-btn-danger'
                            : 'flux-btn-warning')) }}"
                x-data="{}" wire:loading.attr="disabled"
                x-on:click="$dispatch('close-modal-confirm-document-request'); $wire.confirmDocumentRequest()">
                <span wire:loading.remove>
                    @if ($confirmPaymentStatus)
                        <i
                            class="bi {{ $paymentStatusToUpdate === 'paid'
                                ? 'bi-check-circle'
                                : ($paymentStatusToUpdate === 'failed'
                                    ? 'bi-x-circle'
                                    : 'bi-cash') }} me-1"></i>
                        Confirm
                    @else
                        <i
                            class="bi {{ $confirmApproved ? 'bi-check-circle' : ($confirmRejected ? 'bi-x-circle' : 'bi-clock') }} me-1"></i>
                        {{ $confirmApproved ? 'Confirm' : ($confirmRejected ? 'Reject' : 'Set Pending') }}
                    @endif
                </span>
                <span wire:loading>
                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                    Loading...
                </span>
            </button>
        </div>
    </x-slot>
</x-modal>
