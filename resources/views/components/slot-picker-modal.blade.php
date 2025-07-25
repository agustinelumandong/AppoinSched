
{{-- // resources/views/components/slot-picker-modal.blade.php --}}

@props([
    'id',
    'title' => 'Select Available Slot',
    'officeId',
    'serviceId' => null,
    'size' => 'max-w-6xl',
])

<x-modal :id="$id" :title="$title" :size="$size">
    <div>
        <livewire:slot-picker
            :office-id="$officeId"
            :service-id="$serviceId"
            wire:key="slot-picker-{{ $id }}"
        />
    </div>
</x-modal>
