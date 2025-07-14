<div class="day-view">
    <!-- Day Header -->
    <div class="border-b border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">
                    {{ $calendarData['title'] ?? '' }}
                </h3>
                @if($calendarData['date']->isToday())
                    <span class="text-sm text-blue-600 font-medium">Today</span>
                @endif
            </div>
            <div class="text-sm text-gray-500">
                {{ count($calendarData['appointments'] ?? []) }} appointments
            </div>
        </div>
    </div>
    <!-- Time Grid -->
    <div class="grid grid-cols-2">
        <!-- Time Labels -->
        <div class="border-r border-gray-200">
            @foreach($calendarData['timeSlots'] ?? [] as $timeSlot)
                <div class="h-16 border-b border-gray-100 p-2 text-xs text-gray-500 text-right">
                    {{ $timeSlot['display'] }}
                </div>
            @endforeach
        </div>
        <!-- Appointments Column -->
        <div>
            @foreach($calendarData['timeSlots'] ?? [] as $timeSlot)
                <div class="h-16 border-b border-gray-100 p-2 relative">
                    @foreach($calendarData['appointments'] ?? [] as $appointment)
                        @php
                            // Normalize time formats for comparison
                            $appointmentTime = Carbon\Carbon::parse($appointment->booking_time)->format('H:i');
                            $slotTime = $timeSlot['time'];
                        @endphp
                        @if($appointmentTime === $slotTime)
                            <div
                                class="appointment-item p-2 rounded-lg border-l-4 bg-white shadow-sm mb-2 cursor-pointer hover:shadow-md transition-shadow border-blue-400 bg-blue-100">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-gray-900 truncate">
                                            {{ $appointment->user->first_name }} {{ $appointment->user->last_name }}
                                        </div>
                                        <div class="text-xs text-gray-600 truncate">
                                            {{ $appointment->service->name ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $appointment->office->name ?? 'N/A' }}
                                        </div>
                                    </div>
                                    <div class="ml-2 flex-shrink-0">
                                        <div class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($appointment->booking_time)->format('h:i A') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
    <!-- No Appointments Message -->
    @if(empty($calendarData['appointments']) || count($calendarData['appointments']) === 0)
        <div class="p-8 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h4 class="text-lg font-medium text-gray-700 mb-2">No appointments for this date</h4>
            <p class="text-gray-500">{{ $calendarData['date']->format('F j, Y') }}</p>
        </div>
    @endif
</div>