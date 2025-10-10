<x-modal id="payment-method" title="Payment Method" size="max-w-2xl">
    <div class="modal-body">
        {{-- Debug --}}
        {{-- {{ $selectedDocumentRequest->id ?? 'null' }} --}}
        <div class="row" x-data="{ selectedPaymentMethod: null }">
            <div class="col-md-12">
                <div class="flux-card payment-option-card p-5"
                    x-on:click="selectedPaymentMethod = 'online'; $wire.selectPaymentMethod('online')"
                    :class="{ 'border-primary bg-primary-subtle': selectedPaymentMethod === 'online' }"
                    style="cursor: pointer; transition: all 0.2s ease;" name="paymentMethod" value="online">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-globe me-1"></i>
                                <h5 class="card-title mb-0">
                                    Online Payment
                                </h5>
                            </div>
                        </div>
                        <p class="card-text mt-2">
                            Pay online through our secure payment gateway.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="footer">
        <div class="gap-2">
            <button type="button" class="btn btn-outline-secondary" x-data
                x-on:click="$dispatch('close-modal-payment-method'); $wire.resetPaymentMethodStates()">
                <i class="bi bi-x-lg me-1"></i>Cancel
            </button>
        </div>
        <button type="button" class="btn btn-primary" x-data
            x-on:click="$dispatch('close-modal-payment-method'); $wire.payNow()" wire:loading.attr="disabled"
            wire:loading.class="opacity-50 cursor-not-allowed">
            <i class="bi bi-check-lg me-1"></i>Pay Now
        </button>
    </x-slot>
</x-modal>