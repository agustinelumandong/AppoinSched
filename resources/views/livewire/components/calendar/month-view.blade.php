<div class="month-view">
    <!-- Day Headers -->
    <div class="grid grid-cols-7 border-b border-gray-200">
        @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
            <div class="p-1 text-center text-xs font-medium text-gray-500 border-r border-gray-100 last:border-r-0">
                {{ $day }}
            </div>
        @endforeach
    </div>
    <!-- Calendar Grid -->
    <div class="calendar-grid">
        @foreach ($calendarData['weeks'] ?? [] as $week)
            <div class="grid grid-cols-7 border-b border-gray-100 last:border-b-0">
                @foreach ($week as $day)
                    @php
                        $isClient = auth()->user()->hasRole('client');
                    @endphp
                    <div class="calendar-day border-r border-gray-100 last:border-r-0 p-2
                          {{ !$day['isCurrentMonth'] ? 'bg-gray-50' : '' }}
                          {{ $day['isToday'] ? 'bg-blue-100' : '' }}
                          {{ $isClient ? 'cursor-default' : 'hover:bg-blue-100 cursor-pointer' }} transition-colors overflow-hidden"
                        style="height: calc((100vh - 460px) / 6);">
                        <!-- Date Number -->
                        <div class="flex justify-between items-start mb-1 gap-2">
                            <div class="flex flex-col items-center justify-between gap-1">
                                <span
                                    class="text-sm font-medium
                                        {{ !$day['isCurrentMonth'] ? 'text-gray-400' : 'text-gray-900' }}
                                        {{ $day['isToday'] ? 'bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs' : '' }}">
                                    {{ $day['date']->day }}
                                </span>
                                @if ($day['appointments']->count() > 2)
                                    <div class="text-gray-500 font-medium" style="font-size: 0.65rem;">
                                        +{{ $day['appointments']->count() - 2 }} 
                                    </div>
                                @endif
                            </div>
                            <!-- Appointments -->
                            <div class="w-full space-y-1">

                                @foreach ($day['appointments']->take(2) as $appointment)
                                    <div class="appointment-item text-xs p-1 rounded truncate cursor-pointer bg-blue-50 text-blue-800"
                                        title="{{ Carbon\Carbon::parse($appointment->booking_time)->format('g:i A') }} - {{ $appointment->service->name ?? 'N/A' }}">
                                        {{ Carbon\Carbon::parse($appointment->booking_time)->format('g:i A') }} -
                                        {{ Str::limit($appointment->service->title ?? 'N/A', 15) }}
                                    </div>
                                @endforeach


                               

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
