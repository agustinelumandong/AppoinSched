<div>
  <div class="px-5 py-2 mt-5">
    <div class="flex flex-col items-center justify-center py-8">
      <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
      </div>
      <h2 class="text-2xl font-bold text-gray-900 mb-2">Thank You!</h2>
      <p class="text-gray-600 text-center max-w-md mb-6">
        Your document request has been submitted and payment is being verified. The admin will process your request
        shortly.
      </p>

      <div class="alert alert-info flex items-center gap-2 mb-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 w-5 h-5" fill="none" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span>Processing can take up to <span class="font-semibold">1-3 business days</span>.</span>
      </div>

      <div class="bg-gray-50 p-4 rounded-lg w-full max-w-md mb-6">
        <h3 class="font-medium text-gray-800 mb-2">Request Summary</h3>
        <div class="grid grid-cols-2 gap-2 text-sm">
          <div class="text-gray-600">Reference Number:</div>
          <div class="font-medium">{{ $reference_number }}</div>
          <div class="text-gray-600">Document Type:</div>
          <div class="font-medium">{{ $this->service->title }}</div>
          <div class="text-gray-600">Amount Paid:</div>
          <div class="font-medium">₱{{ number_format($this->service->price, 2) }}</div>
          <div class="text-gray-600">Payment Method:</div>
          <div class="font-medium">{{ ucfirst($selectedPaymentMethod ?? 'Online') }}</div>
          <div class="text-gray-600">Status:</div>
          <div class="font-medium text-amber-600">Payment Verification in Progress</div>
        </div>
      </div>
      <div class="flex gap-4">
        <a href="{{ route('client.documents') }}" class="flux-btn flux-btn-outline">
          View My Requests
        </a>
        <a href="{{ route('client.dashboard') }}" class="flux-btn flux-btn-primary">
          Back to Dashboard
        </a>
      </div>
    </div>
  </div>
</div>