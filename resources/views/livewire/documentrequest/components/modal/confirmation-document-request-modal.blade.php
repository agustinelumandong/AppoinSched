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
                        Note: The document request status will automatically be set to "Cancelled". Remarks are required.
                    </span>
                @elseif($paymentStatusToUpdate === 'unpaid')
                    <span class="block mt-2 text-sm text-amber-600">
                        Note: This will set the document status to "Pending".
                    </span>
                @endif
            @else
                @if (!empty($documentStatusToUpdate))
                    Are you sure you want to set this document request status to
                    <strong>{{ ucfirst(str_replace('-', ' ', $documentStatusToUpdate)) }}</strong>?
                    @if ($documentStatusToUpdate === 'cancelled')
                        <span class="block mt-2 text-sm text-amber-600">
                            Note: Remarks are required when cancelling a document request.
                        </span>
                    @elseif ($documentStatusToUpdate === 'complete')
                        <span class="block mt-2 text-sm text-blue-600">
                            Note: Please provide claim information below.
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
            @endif
        </p>

        @php
            $remarksRequired = ($confirmPaymentStatus && $paymentStatusToUpdate === 'failed') ||
                               ($documentStatusToUpdate === 'cancelled');
        @endphp

        @if ($remarksRequired)
            <div class="mt-4">
                <label for="modal-remarks" class="block text-sm font-medium text-gray-700 mb-2">
                    Remarks <span class="text-red-600">*</span>
                </label>
                <textarea
                    id="modal-remarks"
                    wire:model.live="remarks"
                    class="flux-form-control w-full h-24 resize-none @if (empty(trim($remarks ?? ''))) border-red-300 @endif"
                    placeholder="Please provide remarks explaining the reason for this action..."></textarea>
                @if (empty(trim($remarks ?? '')))
                    <p class="text-xs text-red-600 mt-1">Remarks are required for this action.</p>
                @endif
            </div>
        @endif

        @if ($documentStatusToUpdate === 'complete')
            <div class="mt-4 space-y-4">
                <div>
                    <label for="modal-claimed-by" class="block text-sm font-medium text-gray-700 mb-2">
                        Claimed by <span class="text-red-600">*</span>
                    </label>
                    <select
                        id="modal-claimed-by"
                        wire:model.live="claimedBy"
                        class="flux-form-control w-full @if (empty(trim($claimedBy ?? ''))) border-red-300 @endif">
                        <option value="">Select who claimed the document</option>
                        <option value="OWNER">OWNER</option>
                        <option value="AUTHORIZED PERSON">AUTHORIZED PERSON</option>
                    </select>
                    @if (empty(trim($claimedBy ?? '')))
                        <p class="text-xs text-red-600 mt-1">Please select who claimed the document.</p>
                    @endif
                </div>
                <div>
                    <label for="modal-claimed-date-time" class="block text-sm font-medium text-gray-700 mb-2">
                        Date & Time <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="datetime-local"
                        id="modal-claimed-date-time"
                        wire:model.live="claimedDateTime"
                        class="flux-form-control w-full @if (empty(trim($claimedDateTime ?? ''))) border-red-300 @endif">
                    @if (empty(trim($claimedDateTime ?? '')))
                        <p class="text-xs text-red-600 mt-1">Please provide the date and time when the document was claimed.</p>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <x-slot name="footer">
        @php
            $remarksRequired = ($confirmPaymentStatus && $paymentStatusToUpdate === 'failed') ||
                               ($documentStatusToUpdate === 'cancelled');
            $remarksEmpty = empty(trim($remarks ?? ''));
            $claimInfoRequired = $documentStatusToUpdate === 'complete';
            $claimedByEmpty = empty(trim($claimedBy ?? ''));
            $claimedDateTimeEmpty = empty(trim($claimedDateTime ?? ''));
            $claimInfoEmpty = $claimInfoRequired && ($claimedByEmpty || $claimedDateTimeEmpty);
            $isDisabled = ($remarksRequired && $remarksEmpty) || $claimInfoEmpty;
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
                    : (!empty($documentStatusToUpdate)
                        ? ($documentStatusToUpdate === 'in-progress'
                            ? 'flux-btn-info'
                            : ($documentStatusToUpdate === 'ready-for-pickup'
                                ? 'flux-btn-success'
                                : ($documentStatusToUpdate === 'complete'
                                    ? 'flux-btn-success'
                                    : ($documentStatusToUpdate === 'cancelled'
                                        ? 'flux-btn-danger'
                                        : ($documentStatusToUpdate === 'pending'
                                            ? 'flux-btn-warning'
                                            : 'flux-btn-secondary')))))
                        : ($confirmApproved
                            ? 'flux-btn-success'
                            : ($confirmRejected
                                ? 'flux-btn-danger'
                                : 'flux-btn-warning'))) }} @if ($isDisabled) opacity-50 cursor-not-allowed @endif"
                @if ($isDisabled) disabled
                    title="@if ($remarksRequired && $remarksEmpty) Please fill in the remarks field before confirming @elseif ($claimInfoEmpty) Please fill in all claim information fields before confirming @endif" @endif
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
                        @if (!empty($documentStatusToUpdate))
                            <i
                                class="bi {{ $documentStatusToUpdate === 'in-progress' ? 'bi-arrow-right-circle' : ($documentStatusToUpdate === 'ready-for-pickup' ? 'bi-box-arrow-up' : ($documentStatusToUpdate === 'complete' ? 'bi-check2-circle' : ($documentStatusToUpdate === 'cancelled' ? 'bi-x-circle' : 'bi-clock'))) }} me-1"></i>
                            Confirm
                        @else
                            <i
                                class="bi {{ $confirmApproved ? 'bi-check-circle' : ($confirmRejected ? 'bi-x-circle' : 'bi-clock') }} me-1"></i>
                            {{ $confirmApproved ? 'Confirm' : ($confirmRejected ? 'Reject' : 'Set Pending') }}
                        @endif
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
