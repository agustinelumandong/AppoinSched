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
                @elseif($paymentStatusToUpdate === 'failed')
                    <span class="block mt-2 text-sm text-amber-600">
                        Note: The document request status will remain as "Pending". Remarks are required.
                    </span>
                @elseif($paymentStatusToUpdate === 'unpaid')
                    <span class="block mt-2 text-sm text-amber-600">
                        Note: This will set the document status to "Pending".
                    </span>
                @endif
            @else
                Are you sure you want to
                {{ $confirmApproved ? 'approve' : ($confirmRejected ? 'cancel' : 'set to pending') }} this document
                request?
                @if ($confirmRejected)
                    <span class="block mt-2 text-sm text-amber-600">
                        Note: Remarks are required when cancelling a document request.
                    </span>
                @endif
            @endif
        </p>

        @php
            $remarksRequired = ($confirmPaymentStatus && $paymentStatusToUpdate === 'failed') ||
                               ($confirmRejected && $documentStatusToUpdate === 'cancelled');
        @endphp

        @if ($remarksRequired || !empty($remarks))
            <div class="mt-4">
                <label for="modal-remarks" class="block text-sm font-medium text-gray-700 mb-2">
                    Remarks
                    @if ($remarksRequired)
                        <span class="text-red-600">*</span>
                    @endif
                </label>
                <textarea
                    id="modal-remarks"
                    wire:model.live="remarks"
                    class="flux-form-control w-full h-24 resize-none @if ($remarksRequired && empty(trim($remarks ?? ''))) border-red-300 @endif"
                    placeholder="@if ($remarksRequired) Please provide remarks explaining the reason for this action... @else Add any remarks or notes about this request... @endif"></textarea>
                @if ($remarksRequired && empty(trim($remarks ?? '')))
                    <p class="text-xs text-red-600 mt-1">Remarks are required for this action.</p>
                @endif
            </div>
        @endif
    </div>

    <x-slot name="footer">
        @php
            $remarksRequired = ($confirmPaymentStatus && $paymentStatusToUpdate === 'failed') ||
                               ($confirmRejected && $documentStatusToUpdate === 'cancelled');
            $remarksEmpty = empty(trim($remarks ?? ''));
            $isDisabled = $remarksRequired && $remarksEmpty;
        @endphp
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
                            : 'flux-btn-warning')) }} @if ($isDisabled) opacity-50 cursor-not-allowed @endif"
                @if ($isDisabled) disabled title="Please fill in the remarks field before confirming" @endif
                wire:loading.attr="disabled"
                wire:click="confirmDocumentRequest">
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
