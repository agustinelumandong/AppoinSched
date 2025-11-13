<div class="space-y-6">
  <div class="text-center">
    <div class="mb-4">
      <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
      </div>
      <h2 class="text-xl font-semibold text-gray-900">Appointment Created Successfully!</h2>
      <p class="text-gray-600 mt-1">Your appointment has been scheduled</p>
    </div>
  </div>

  @if($reference_number)
    <div class="card bg-base-100 border border-gray-200">
    <div class="card-body">
      <h3 class="card-title text-lg font-semibold">Appointment Details</h3>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
      <div>
        <label class="text-sm font-medium text-gray-500">Reference Number</label>
        <p class="text-lg font-semibold text-blue-600">{{ $reference_number }}</p>
      </div>

      <div>
        <label class="text-sm font-medium text-gray-500">Status</label>
        <p class="text-sm">
        <span class="badge badge-success">Confirmed</span>
        </p>
      </div>

      <div>
        <label class="text-sm font-medium text-gray-500">Date</label>
        <p class="text-sm">{{ \Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}</p>
      </div>

      <div>
        <label class="text-sm font-medium text-gray-500">Time</label>
        <p class="text-sm">{{ \Carbon\Carbon::parse($selectedTime)->format('g:i A') }}</p>
      </div>

      <div>
        <label class="text-sm font-medium text-gray-500">Office</label>
        <p class="text-sm">{{ $office->name }}</p>
      </div>

      <div>
        <label class="text-sm font-medium text-gray-500">Service</label>
        <p class="text-sm">{{ $service->name }}</p>
      </div>
      </div>

      <div class="mt-4">
      <label class="text-sm font-medium text-gray-500">Purpose</label>
      <p class="text-sm">{{ $purpose }}</p>
      </div>

      @if($notes)
      <div class="mt-4">
      <label class="text-sm font-medium text-gray-500">Additional Notes</label>
      <p class="text-sm">{{ $notes }}</p>
      </div>
    @endif
    </div>
    </div>
  @endif

  @if(count($certificates) > 0)
    <div class="card bg-base-100 border border-gray-200">
    <div class="card-body">
      <h3 class="card-title text-lg font-semibold">Certificate Requests</h3>

      <div class="space-y-3 mt-4">
      @foreach($certificates as $index => $certificate)
      <div class="border-l-4 border-blue-500 pl-4">
      <div class="flex justify-between items-start">
      <div>
        <p class="font-medium">{{ ucfirst($certificate['certificate_type']) }}</p>
        <p class="text-sm text-gray-600">
            @php
                $nameParts = array_filter([
                    $certificate['first_name'] ?? '',
                    strtolower($certificate['middle_name'] ?? '') !== 'n/a' ? ($certificate['middle_name'] ?? null) : null,
                    $certificate['last_name'] ?? '',
                ], function ($part) {
                    return !empty($part) && strtolower(trim($part)) !== 'n/a';
                });
                echo implode(' ', $nameParts);
            @endphp
        </p>
        <p class="text-xs text-gray-500">Relationship: {{ ucfirst($certificate['relationship']) }}</p>
      </div>
      </div>
      </div>
    @endforeach
      </div>
    </div>
    </div>
  @endif

  <div class="alert alert-info">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <span>
      <strong>Important:</strong> Please arrive 10 minutes before your scheduled appointment time.
      Bring a valid ID and any required documents.
    </span>
  </div>

  <div class="flex justify-center gap-4 pt-4">
    <a href="{{ route('client.dashboard') }}" class="btn btn-primary">
      Go to Dashboard
    </a>
    <button type="button" wire:click="sendEmailSlip" class="btn btn-outline">
      Send Email Confirmation
    </button>
  </div>
</div>