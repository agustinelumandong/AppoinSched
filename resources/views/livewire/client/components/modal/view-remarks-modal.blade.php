<x-modal id="view-remarks" title="Staff Remarks" size="max-w-2xl">
    <div class="modal-body">
        @if ($selectedRemarksRequest)
            <div class="mb-4">
                <div class="flex items-center justify-between mb-3">
                    <h6 class="text-muted mb-2">Request Reference Number</h6>
                </div>
                <div class="p-3 flux-card bg-light">
                    <p class="mb-0 text-dark font-semibold">{{ $selectedRemarksRequest->reference_number ?? 'N/A' }}</p>
                </div>
            </div>

            <div>
                <h6 class="text-muted mb-2">Remarks</h6>
                @if (!empty($selectedRemarksRequest->remarks))
                    <div class="p-4 flux-card bg-gray-50 border">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap mb-0">{{ $selectedRemarksRequest->remarks }}</p>
                    </div>
                @else
                    <div class="p-4 flux-card bg-yellow-50 border border-yellow-200">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle text-warning me-2"></i>
                            <p class="mb-0 text-warning">No remarks are available for this request.</p>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <x-slot name="footer">
        <div class="gap-2">
            <button type="button" class="btn btn-outline-secondary" x-data
                x-on:click="$wire.closeRemarksModal()">
                <i class="bi bi-x-lg me-1"></i>Close
            </button>
        </div>
    </x-slot>
</x-modal>

