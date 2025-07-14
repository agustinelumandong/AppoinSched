<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use App\Models\Appointments;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

new class extends Component {
    public int $officeId;
    public ?int $serviceId = null;
    public ?int $staffId = null;
    public ?int $excludeAppointmentId = null;

    public ?string $selectedDate = null;
    public ?string $selectedTime = null;
    public array $availableSlots = [];
    public bool $isLoading = false;
    public ?string $error = null;

    // Configuration - can be made configurable per office/service
    public int $slotDuration = 30;
    public string $startTime = '08:00';
    public string $endTime = '17:00';
    public string $lunchStart = '12:00';
    public string $lunchEnd = '13:00';

    protected array $rules = [
        'selectedDate' => 'required|date|after_or_equal:today',
        'selectedTime' => 'required|string',
    ];

    public function mount(
        int $officeId, 
        ?int $serviceId = null, 
        ?int $staffId = null, 
        ?int $excludeAppointmentId = null, 
        ?string $preSelectedDate = null, 
        ?string $preSelectedTime = null,
        ?string $selectedDate = null, 
        ?string $selectedTime = null
    ): void {
        try {
            $this->officeId = $officeId;
            $this->serviceId = $serviceId;
            $this->staffId = $staffId;
            $this->excludeAppointmentId = $excludeAppointmentId;

            // Set pre-selected values if provided (for editing)
            $this->selectedDate = $selectedDate ?? $preSelectedDate ?? Carbon::today()->format('Y-m-d');
            $this->selectedTime = $selectedTime ?? $preSelectedTime;

            $this->generateTimeSlots();
        } catch (\Exception $e) {
            Log::error('Error mounting slot picker: ' . $e->getMessage());
            $this->error = 'Failed to initialize slot picker';
        }
    }

    public function updatedSelectedDate(): void
    {
        $this->handleDateChange();
    }

    public function selectDate(string $date): void
    {
        try {
            $selectedDate = Carbon::parse($date);

            if ($selectedDate->isWeekend()) {
                $this->error = 'Cannot select weekend dates';
                return;
            }
            if ($selectedDate->isPast()) {
                $this->error = 'Cannot select past dates';
                return;
            }

            $this->selectedDate = $selectedDate->format('Y-m-d');
            $this->selectedTime = null; // Reset time when date changes
            $this->error = null;

            $this->handleDateChange();
        } catch (\Exception $e) {
            $this->error = 'Invalid date selected';
            Log::error('Date selection error: ' . $e->getMessage(), [
                'date' => $date,
                'office_id' => $this->officeId
            ]);
        }
    }

    public function selectTime(string $time): void
    {
        try {
            if(Carbon::parse($this->selectedDate)->isWeekend()) {
                $this->error = 'Cannot select weekend dates';
                return;
            }

            if (!$this->isValidTimeFormat($time)) {
                throw new \InvalidArgumentException('Invalid time format');
            }

            // Convert input time to 24-hour format for comparison
            $timeToCheck = Carbon::parse($time)->format('H:i');
            
            // Check if the slot is available
            $bookedSlots = $this->getBookedSlots();
            $isBooked = in_array($timeToCheck, $bookedSlots);

            Log::info('Attempting to select time:', [
                'time' => $time,
                'time_to_check' => $timeToCheck,
                'is_booked' => $isBooked,
                'booked_slots' => $bookedSlots,
                'current_appointment_id' => $this->excludeAppointmentId
            ]);

            if ($isBooked) {
                $this->error = 'This time slot is already booked';
                return;
            }

            $this->selectedTime = $time;
            $this->error = null;

            $this->dispatchSelectionUpdate();

            Log::info('Time slot selected successfully', [
                'date' => $this->selectedDate,
                'time' => $this->selectedTime,
                'office_id' => $this->officeId,
                'staff_id' => $this->staffId
            ]);
        } catch (\Exception $e) {
            $this->error = 'Failed to select time slot';
            Log::error('Time selection error: ' . $e->getMessage(), [
                'time' => $time,
                'office_id' => $this->officeId
            ]);
        }
    }

    public function refreshSlots(): void
    {
        try {
            $this->clearCache();
            $this->generateTimeSlots();
        } catch (\Exception $e) {
            Log::error('Error refreshing slots: ' . $e->getMessage());
            $this->error = 'Failed to refresh time slots';
        }
    }

    private function handleDateChange(): void
    {
        $this->isLoading = true;
        $this->error = null;

        try {
            $this->generateTimeSlots();
            $this->dispatchSelectionUpdate();
        } catch (\Exception $e) {
            $this->error = 'Failed to load available slots';
            $this->availableSlots = [];
            Log::error('Date change error: ' . $e->getMessage(), [
                'date' => $this->selectedDate,
                'office_id' => $this->officeId
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    private function generateTimeSlots(): void
    {
        if (!$this->selectedDate) {
            $this->availableSlots = [];
            return;
        }

        try {
            // Generate cache key based on parameters
            $cacheKey = $this->generateCacheKey();
            
            // Try to get slots from cache first
            $slots = Cache::remember($cacheKey, now()->addMinutes(5), function () {
                return $this->generateTimeSlotsFromScratch();
            });

            $this->availableSlots = $slots;

            // $this->availableSlots = $this->generateTimeSlotsFromScratch();
        } catch (\Exception $e) {
            Log::error('Error generating time slots: ' . $e->getMessage());
            $this->error = 'Failed to generate time slots';
            $this->availableSlots = [];
        }
    }

    private function generateCacheKey(): string
    {
        return "time_slots_{$this->officeId}_{$this->staffId}_{$this->selectedDate}";
    }

    private function clearCache(): void
    {
        try {
            Cache::forget($this->generateCacheKey());
        } catch (\Exception $e) {
            Log::error('Error clearing cache: ' . $e->getMessage());
        }
    }

    private function generateTimeSlotsFromScratch(): array
    {
        try {
            $slots = [];
            $currentTime = Carbon::parse($this->startTime);
            $endTime = Carbon::parse($this->endTime);
            $lunchStart = Carbon::parse($this->lunchStart);
            $lunchEnd = Carbon::parse($this->lunchEnd);

            $bookedSlots = $this->getBookedSlots();

            Log::info('Generating time slots:', [
                'date' => $this->selectedDate,
                'booked_slots' => $bookedSlots,
                'start_time' => $this->startTime,
                'end_time' => $this->endTime
            ]);

            while ($currentTime->copy()->addMinutes($this->slotDuration) <= $endTime) {
                // Skip lunch break
                if ($currentTime->between($lunchStart, $lunchEnd->subMinute())) {
                    $currentTime->addMinutes($this->slotDuration);
                    continue;
                }

                $slotTime = $currentTime->format('H:i');
                $endSlotTime = $currentTime->copy()->addMinutes($this->slotDuration);
                $isAvailable = !in_array($slotTime, $bookedSlots);

                Log::info('Processing slot:', [
                    'time' => $slotTime,
                    'is_available' => $isAvailable,
                    'booked_slots' => $bookedSlots
                ]);

                $slots[] = [
                    'time' => $slotTime,
                    'display' => $currentTime->format('g:i A'),
                    'range' => $currentTime->format('g:i A') . ' - ' . $endSlotTime->format('g:i A'),
                    'available' => $isAvailable,
                    'isPast' => $this->isSlotInPast($slotTime),
                ];

                $currentTime->addMinutes($this->slotDuration);
            }

            return $slots;
        } catch (\Exception $e) {
            Log::error('Error generating time slots from scratch: ' . $e->getMessage());
            return [];
        }
    }

    private function getBookedSlots(): array
    {
        try {
            $bookedSlots = Appointments::query()
                ->where('booking_date', $this->selectedDate)
                ->where('office_id', $this->officeId)
                ->when($this->staffId, function($q) {
                    return $q->where('staff_id', $this->staffId);
                })
                ->when($this->excludeAppointmentId, function($q) {
                    return $q->where('id', '!=', $this->excludeAppointmentId);
                })
                ->get(['booking_time']);

            $formattedSlots = $bookedSlots->map(function($appointment) {
                // Ensure consistent 24-hour format
                return Carbon::parse($appointment->booking_time)->format('H:i');
            })->toArray();

            Log::info('Booked slots found:', [
                'date' => $this->selectedDate,
                'office_id' => $this->officeId,
                'service_id' => $this->serviceId,
                'staff_id' => $this->staffId,
                'exclude_id' => $this->excludeAppointmentId,
                'slots' => $formattedSlots,
                'raw_slots' => $bookedSlots->pluck('booking_time')->toArray()
            ]);

            return $formattedSlots;
        } catch (\Exception $e) {
            Log::error('Error fetching booked slots: ' . $e->getMessage(), [
                'date' => $this->selectedDate,
                'office_id' => $this->officeId,
                'service_id' => $this->serviceId
            ]);
            return [];
        }
    }

    private function isSlotInPast(string $slotTime): bool
    {
        if (!$this->selectedDate) {
            return false;
        }

        try {
            $slotDateTime = Carbon::parse($this->selectedDate . ' ' . $slotTime);
            return $slotDateTime->isPast();
        } catch (\Exception $e) {
            Log::error('Error checking if slot is in past: ' . $e->getMessage(), [
                'date' => $this->selectedDate,
                'time' => $slotTime
            ]);
            return true; // Default to true to prevent booking invalid slots
        }
    }

    private function isValidTimeFormat(string $time): bool
    {
        return (bool) preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time);
    }

    private function dispatchSelectionUpdate(): void
    {
        try {
            $this->dispatch('slot-picker-updated', [
                'date' => $this->selectedDate,
                'time' => $this->selectedTime,
                'isValid' => $this->selectedDate && $this->selectedTime,
            ]);
        } catch (\Exception $e) {
            Log::error('Error dispatching selection update: ' . $e->getMessage());
        }
    }

    public function getAvailableSlotsProperty(): array
    {
        return array_filter($this->availableSlots, fn($slot) => $slot['available'] && !$slot['isPast']);
    }
}; ?>

<div class="slot-picker-container" 
    x-data="{
        currentMonth: new Date().getMonth(),
        currentYear: new Date().getFullYear(),
        selectedDate: @entangle('selectedDate'),
        calendar: [],
        weekDays: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],

        init() {
            // Initialize to the selected date's month if available
            if (this.selectedDate) {
                const [year, month] = this.selectedDate.split('-').map(Number);
                this.currentYear = year;
                this.currentMonth = month - 1; // Convert to 0-based month
            }
            this.generateCalendar();
        },

        previousMonth() {
            this.currentMonth--;
            if (this.currentMonth < 0) {
                this.currentMonth = 11;
                this.currentYear--;
            }
            this.generateCalendar();
        },

        nextMonth() {
            this.currentMonth++;
            if (this.currentMonth > 11) {
                this.currentMonth = 0;
                this.currentYear++;
            }
            this.generateCalendar();
        },

        selectDate(day, event) {
            event.preventDefault();
            event.stopPropagation();
            const dateStr = `${this.currentYear}-${(this.currentMonth + 1).toString().padStart(2, '0')}-${day.number.toString().padStart(2, '0')}`;
            this.$wire.selectDate(dateStr);
            // Don't reset the calendar view after selection
        },

        generateCalendar() {
            const firstDay = new Date(this.currentYear, this.currentMonth, 1);
            const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
            const startingDay = firstDay.getDay(); // Make Sunday = 0, Saturday = 6
            const daysInMonth = lastDay.getDate();

            let calendar = [];
            let week = [];

            // Add empty cells for days before the first day of the month
            for (let i = 0; i < startingDay; i++) {
                week.push(null);
            }

            // Add days of the month
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(this.currentYear, this.currentMonth, day);
                const dayOfWeek = date.getDay();

                week.push({ 
                    number: day, 
                    isWeekend: dayOfWeek === 0 || dayOfWeek === 6
                });

                if (week.length === 7) {
                    calendar.push(week);
                    week = [];
                }
            }

            // Fill the last week with empty cells if needed
            while (week.length < 7 && week.length > 0) {
                week.push(null);
            }
            if (week.length > 0) {
                calendar.push(week);
            }

            this.calendar = calendar;
        },

        getMonthName(month) {
            return new Date(2000, month, 1).toLocaleString('default', { month: 'long' });
        },

        isSelectedDate(day) {
            if (!this.selectedDate || !day) return false;
            const [year, month, selectedDay] = this.selectedDate.split('-').map(Number);
            return year === this.currentYear && month - 1 === this.currentMonth && selectedDay === day;
        },

        isToday(day) {
            const today = new Date();
            return today.getDate() === day &&
                today.getMonth() === this.currentMonth &&
                today.getFullYear() === this.currentYear;
        },

        isPastDate(day) {
            const today = new Date();
            const selectedDate = new Date(this.currentYear, this.currentMonth, day);
            return selectedDate < new Date(today.getFullYear(), today.getMonth(), today.getDate());
        },

        isDayWeekend(day) {
            const dayOfWeek = new Date(this.currentYear, this.currentMonth, day).getDay();
            return dayOfWeek === 0 || dayOfWeek === 6;
        },

        getDateClasses(day) {
            let classes = [];

            if (this.isSelectedDate(day.number)) {
                classes.push('bg-blue-500 text-white hover:bg-blue-600');
            } else if (this.isToday(day.number)) {
                classes.push('bg-blue-100 text-blue-600 hover:bg-blue-200');
            } else if (!this.isPastDate(day.number) && !day.isWeekend) {
                classes.push('text-gray-700 hover:bg-blue-50 hover:text-blue-600');
            } else {
                classes.push('text-gray-400 cursor-not-allowed opacity-50');
            }

            return classes.join(' ');
        }
    }"
    x-init="init()"
    wire:key="slot-picker-{{ $officeId }}-{{ $excludeAppointmentId ?? 'new' }}"
    @click.stop>

    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Calendar Section -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <button type="button" @click.stop="previousMonth()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                </button>

                <h3 class="text-lg font-semibold text-gray-800" x-text="getMonthName(currentMonth) + ' ' + currentYear">
                </h3>

                <button type="button" @click.stop="nextMonth()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-1 mb-3">
                <template x-for="day in weekDays" :key="day">
                    <div class="p-2 text-center text-sm font-medium text-gray-500" x-text="day"></div>
                </template>
            </div>

            <template x-for="(week, weekIndex) in calendar" :key="weekIndex">
                <div class="grid grid-cols-7 gap-1 mb-1">
                    <template x-for="(day, dayIndex) in week" :key="weekIndex + '-' + dayIndex">
                        <div class="aspect-square">
                            <template x-if="day">
                                <button type="button" @click="selectDate(day, $event)"
                                    class="w-full h-full flex items-center justify-center text-sm font-medium rounded-lg transition-all duration-200"
                                    :class="getDateClasses(day)" :disabled="isPastDate(day.number) || day.isWeekend" x-text="day.number">
                                </button>
                            </template>
                            <template x-if="!day">
                                <div class="w-full h-full"></div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <!-- Time Slots Section -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Available Time Slots</h3>
                <div class="flex items-center gap-2">
                    <button type="button" @click.stop="$wire.refreshSlots()"
                        class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200" title="Refresh slots">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                    </button>
                    <div class="text-sm text-gray-500">
                        {{ $slotDuration }} min slots
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div wire:loading.delay class="text-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
                <p class="text-gray-600">Loading available slots...</p>
            </div>

            <!-- Error Message -->
            @if ($error)
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $error }}
                    </div>
                </div>
            @endif

            <!-- Time Slots Display -->
            <div wire:loading.remove>
                @if($selectedDate && Carbon::parse($selectedDate)->isWeekend())
                    <div class="text-center py-8">
                        <h4 class="text-lg font-medium text-gray-700 mb-2">Cannot select weekend dates</h4>
                        <p class="text-gray-500">Please select a different date.</p>
                    </div>
                @elseif ($selectedDate && !Carbon::parse($selectedDate)->isWeekend())
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center text-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span class="font-medium">
                                {{ Carbon::parse($selectedDate)->format('F j, Y') }}  
                                @if($selectedDate && $selectedTime)
                                    at  {{ Carbon::parse($selectedTime)->format('g:i A') ?? '' }}
                                @endif
                            </span>
                        </div>
                    </div>

                    @if (count($availableSlots) > 0)
                        <div class="grid grid-cols-2 gap-2 max-h-96 overflow-y-auto">
                            @foreach ($availableSlots as $slot)
                                <button type="button" 
                                    wire:click.stop="selectTime('{{ $slot['time'] }}')"
                                    class="p-3 text-left border rounded-lg transition-all duration-200 
                                        {{ $selectedTime === $slot['time'] ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-700 hover:border-blue-300 hover:bg-blue-50' }} 
                                        {{ !$slot['available'] || $slot['isPast'] ? 'opacity-50 cursor-not-allowed bg-gray-50' : 'hover:shadow-sm' }}"
                                    {{ !$slot['available'] || $slot['isPast'] ? 'disabled' : '' }}>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 {{ $selectedTime === $slot['time'] ? 'text-blue-500' : 'text-gray-400' }}"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="font-medium">{{ $slot['display'] }}</span>
                                        </div>
                                        @if (!$slot['available'])
                                            <span class="text-xs text-red-500 font-medium">Booked</span>
                                        @elseif ($slot['isPast'])
                                            <span class="text-xs text-gray-400">Past</span>
                                        @endif
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h4 class="text-lg font-medium text-gray-700 mb-2">No available slots</h4>
                            <p class="text-gray-500">All time slots are booked for this date.</p>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <h4 class="text-lg font-medium text-gray-700 mb-2">Select a Date</h4>
                        <p class="text-gray-500">Choose a date from the calendar to view available time slots.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
