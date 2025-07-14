<div class="week-view">
  <!-- Day Headers -->
  <div class="grid grid-cols-8 border-b border-gray-200">
    <div class="p-3"></div> <!-- Time column header -->
    @foreach($calendarData['days'] ?? [] as $day)
    <div class="p-3 text-center border-r border-gray-100 last:border-r-0">
      <div class="text-sm font-medium text-gray-500">
      {{ $day['date']->format('D') }}
      </div>
      <div
      class="text-lg font-semibold mt-1
      {{ $day['isToday'] ? 'bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mx-auto' : 'text-gray-900' }}">
      {{ $day['date']->day }}
      </div>
    </div>
  @endforeach
  </div>
  <!-- Time Grid -->
  <div class="grid grid-cols-8">
    @foreach($calendarData['timeSlots'] ?? [] as $timeSlot)
      <!-- Time Label -->
      <div class="p-2 text-xs text-gray-500 border-r border-gray-100 text-right">
        {{ $timeSlot['display'] }}
      </div>

      <!-- Day Columns -->
      @foreach($calendarData['days'] ?? [] as $day)
      <div class="min-h-[60px] border-r border-b border-gray-100 last:border-r-0 p-1 relative">
        @foreach($day['appointments'] as $appointment)
        @php
        // Normalize time formats for comparison
        $appointmentTime = Carbon\Carbon::parse($appointment->booking_time)->format('H:i');
        $slotTime = $timeSlot['time'];
        @endphp

        @if($appointmentTime === $slotTime)
        <div class="appointment-item text-xs p-1 rounded mb-1 cursor-pointer bg-gray-100 text-gray-800"
        title="{{ $appointment->user->first_name }} {{ $appointment->user->last_name }} - {{ $appointment->service->name ?? 'N/A' }}">
        <div class="font-medium truncate">
        {{ $appointment->user->first_name }} {{ $appointment->user->last_name }}
        </div>
        <div class="truncate opacity-75">
        {{ $appointment->service->name ?? 'N/A' }}
        </div>
        </div>
        @endif
      @endforeach
      </div>
      @endforeach
  @endforeach
  </div>
</div>