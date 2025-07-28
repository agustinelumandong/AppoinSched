
{{-- // resources/views/components/slot-picker-modal.blade.php --}}

@props([
    'id',
    'title' => 'Select Available Slot',
    'officeId',
    'serviceId' => null,
    'size' => 'max-w-6xl',
    'maxSlotsPerHour' => 5
])

<x-modal :id="$id" :title="$title" :size="$size">
    <div class="mb-3 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start gap-2">
            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <h4 class="text-sm font-medium text-blue-800">Hourly Appointment System</h4>
                <p class="mt-1 text-xs text-blue-700">Each hour can accommodate up to {{ $maxSlotsPerHour }} appointments. Select your preferred time slot based on availability.</p>
            </div>
        </div>
    </div>

    <div>
        <livewire:slot-picker
            :office-id="$officeId"
            :service-id="$serviceId"
            :max-slots-per-hour="$maxSlotsPerHour"
            wire:key="slot-picker-{{ $id }}"
        />
    </div>
</x-modal>
