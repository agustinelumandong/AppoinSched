<div class="month-view">
  <!-- Day Headers -->
  <div class="grid grid-cols-7 border-b border-gray-200">
    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
    <div class="p-3 text-center text-sm font-medium text-gray-500 border-r border-gray-100 last:border-r-0">
      {{ $day }}
    </div>
  @endforeach
  </div>
  <!-- Calendar Grid -->
  <div class="calendar-grid">
    @foreach($calendarData['weeks'] ?? [] as $week)
    <div class="grid grid-cols-7 border-b border-gray-100 last:border-b-0">
      @foreach($week as $day)
      <div class="calendar-day min-h-[120px] border-r border-gray-100 last:border-r-0 p-2
      {{ !$day['isCurrentMonth'] ? 'bg-gray-50' : '' }}
      {{ $day['isToday'] ? 'bg-blue-50' : '' }}
      hover:bg-gray-50 cursor-pointer transition-colors"
      wire:click="selectDate('{{ $day['date']->format('Y-m-d') }}')">
      <!-- Date Number -->
      <div class="flex justify-between items-start mb-1">
      <span
      class="text-sm font-medium
      {{ !$day['isCurrentMonth'] ? 'text-gray-400' : 'text-gray-900' }}
      {{ $day['isToday'] ? 'bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs' : '' }}">
      {{ $day['date']->day }}
      </span>
      </div>
      <!-- Appointments -->
      <div class="space-y-1">

      @foreach($day['appointments'] as $appointment)
      <div class="appointment-item text-xs p-1 rounded truncate cursor-pointer bg-gray-100 text-gray-800"
      title="{{ Carbon\Carbon::parse($appointment->booking_time)->format('g:i A') }} - {{ $appointment->service->name ?? 'N/A' }}">
      {{ Carbon\Carbon::parse($appointment->booking_time)->format('g:i A') }} -
      {{ Str::limit($appointment->service->title ?? 'N/A', 15) }}
      </div>
      @endforeach


      @if($day['appointments']->count() > 3)
      <div class="text-xs text-gray-500 font-medium">
      +{{ $day['appointments']->count() - 3 }} more
      </div>
      @endif
      </div>
      </div>
    @endforeach
    </div>
  @endforeach
  </div>
</div>