<div>
  <div class="px-5 py-2 mt-5">
    <div class="header mb-4">
      <h3 class="text-xl font-semibold text-base-content">Payment</h3>
      <div class="alert alert-info flex items-center gap-2 mb-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Please complete your payment to finalize your document request.</span>
      </div>

      @if (session()->has('error'))
      <div class="alert alert-danger flex items-center gap-2 mb-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
        </path>
      </svg>
      <span>{{ session('error') }}</span>
      </div>
    @endif

    </div>

    <!-- Loading State -->
    <div wire:loading.delay.class="flex" wire:loading.remove.class="hidden"
      class="hidden justify-center items-center w-full h-64">
      <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-500"></div>
    </div>

    <div class="space-y-6" wire:loading.remove>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Payment Information -->
        <div class="flux-card p-6 h-fit">
          <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Payment Information</h3>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-600 mb-1">Service</label>
              <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $this->service->title }}
              </p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-600 mb-1">Amount</label>
              <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline font-semibold">
                ₱{{ number_format($this->service->price, 2) }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-600 mb-1">Reference Number</label>
              <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $reference_number }}</p>
              <input type="hidden" wire:model="reference_number" value="{{ $reference_number }}">
            </div>
          </div>
        </div>
        <!-- Payment Method and Upload -->
        <div class="flux-card p-6">
          <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Select Payment Method</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div
              class="border rounded-lg p-4 cursor-pointer transition-all {{ $selectedPaymentMethod === 'gcash' ? 'border-blue-500 bg-blue-50' : '' }}"
              wire:click="$set('selectedPaymentMethod', 'gcash')">
              <div class="flex justify-between items-center mb-2">
                <div class="font-medium">GCash</div>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                  <span class="text-blue-600 font-bold">G</span>
                </div>
              </div>
              <p class="text-sm text-gray-600">Pay using GCash</p>
            </div>
            <div
              class="border rounded-lg p-4 cursor-pointer transition-all {{ $selectedPaymentMethod === 'bdo' ? 'border-blue-500 bg-blue-50' : '' }}"
              wire:click="$set('selectedPaymentMethod', 'bdo')">
              <div class="flex justify-between items-center mb-2">
                <div class="font-medium">BDO</div>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                  <span class="text-blue-600 font-bold">B</span>
                </div>
              </div>
              <p class="text-sm text-gray-600">Pay using BDO QR</p>
            </div>
            <div
              class="border rounded-lg p-4 cursor-pointer transition-all {{ $selectedPaymentMethod === 'maya' ? 'border-blue-500 bg-blue-50' : '' }}"
              wire:click="$set('selectedPaymentMethod', 'maya')">
              <div class="flex justify-between items-center mb-2">
                <div class="font-medium">Maya</div>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                  <span class="text-blue-600 font-bold">M</span>
                </div>
              </div>
              <p class="text-sm text-gray-600">Pay using Maya</p>
            </div>
          </div>
          @error('selectedPaymentMethod')
        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
      @enderror
          <div class="mt-6" wire:key="payment-method-{{ $selectedPaymentMethod }}">
            @if($selectedPaymentMethod)
          <h4 class="text-md font-medium mb-4">Scan QR Code to Pay</h4>
          <div class="flex flex-col gap-6">
            <div class="flex justify-center">
            <div class="bg-white p-4 rounded-lg shadow-sm border">
              @if($selectedPaymentMethod === 'gcash')
          <div class="text-center">
          <div class="w-48 h-48 mx-auto bg-gray-200 flex items-center justify-center">
            <span class="text-gray-500">GCash QR Code</span>
          </div>
          <p class="mt-2 text-sm text-gray-600">GCash Account: 09123456789</p>
          </div>
          @elseif($selectedPaymentMethod === 'bdo')
          <div class="text-center">
          <div class="w-48 h-48 mx-auto bg-gray-200 flex items-center justify-center">
            <span class="text-gray-500">BDO QR Code</span>
          </div>
          <p class="mt-2 text-sm text-gray-600">Account Name: Municipality Office</p>
          <p class="text-sm text-gray-600">Account Number: 1234567890</p>
          </div>
          @elseif($selectedPaymentMethod === 'maya')
          <div class="text-center">
          <div class="w-48 h-48 mx-auto bg-gray-200 flex items-center justify-center">
            <span class="text-gray-500">Maya QR Code</span>
          </div>
          <p class="mt-2 text-sm text-gray-600">Maya ID: @municipaloffice</p>
          </div>
          @endif
            </div>
            </div>
            <!-- Payment Proof -->
            @if($selectedPaymentMethod)

          <div>
          <h4 class="text-sm font-medium mb-2">Upload Payment Proof</h4>
          <p class="text-sm text-gray-600 mb-4">Please upload a screenshot of your payment confirmation</p>
          <div class="form-control w-full">
            <label class="block text-xs font-medium mb-1">Payment Screenshot</label>
            <input type="file" class="flux-form-control" wire:model="paymentProofImage" accept="image/*">
            @error('paymentProofImage')
          <div class="text-red-500 text-sm mt-1">Please upload a payment proof image</div>
          @enderror
            @if($paymentProofImage)
          <div class="mt-2">
          <p class="text-xs text-gray-600 mb-1">Preview:</p>
          <img src="{{ $paymentProofImage->temporaryUrl() }}" alt="Payment Proof"
          class="w-full max-h-48 object-contain border rounded">
          </div>
          @endif
          </div>
          </div>
        @endif
          </div>
      @else
            @if($selectedPaymentMethod)
            <div class="form-control w-full">
            <label class="block text-xs font-medium mb-1">Payment Screenshot</label>
            <input type="file" class="flux-form-control" wire:model="paymentProofImage" accept="image/*">
            @error('paymentProofImage')
            <span class="text-red-500 text-sm">{{ $message }}</span>
          @enderror
            @if($paymentProofImage)
            <div class="mt-2">
            <p class="text-xs text-gray-600 mb-1">Preview:</p>
            <img src="{{ $paymentProofImage->temporaryUrl() }}" alt="Payment Proof"
            class="w-full max-h-48 object-contain border rounded">
            </div>
          @endif
            </div>
          @endif
      @endif
          </div>
        </div>
      </div>
    </div>
    <footer class="my-6 flex justify-end gap-2">
      <button class="btn btn-ghost" wire:click="previousStep" wire:loading.attr="disabled">Previous</button>
      <button class="btn btn-primary" wire:click="completePayment" wire:loading.attr="disabled"
        wire:loading.class="opacity-70">
        <span wire:loading wire:target="completePayment" class="loading loading-spinner loading-xs mr-2"></span>
        Complete Payment
      </button>
    </footer>
  </div>
</div>