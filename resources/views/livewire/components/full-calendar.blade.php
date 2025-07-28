<?php
declare(strict_types=1);

use Livewire\Volt\Component;
use App\Models\Appointments;
use App\Models\Offices;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Carbon\CarbonInterface;


new class extends Component {
  public string $view = 'month';
  public Carbon $currentDate;
  public ?int $selectedOfficeId = null;
  public ?int $selectedStaffId = null;
  public array $calendarData = [];
  public bool $isLoading = false;

  public function mount(): void
  {
    $this->currentDate = Carbon::today();
    $this->loadCalendarData();
  }

  public function updatedView(): void
  {
    $this->loadCalendarData();
  }

  public function updatedSelectedOfficeId(): void
  {
    $this->loadCalendarData();
  }

  public function updatedSelectedStaffId(): void
  {
    $this->loadCalendarData();
  }

  public function previousPeriod(): void
  {
    match ($this->view) {
      'month' => $this->currentDate = $this->currentDate->subMonth(),
      'week' => $this->currentDate = $this->currentDate->subWeek(),
      'day' => $this->currentDate = $this->currentDate->subDay(),
    };
    $this->loadCalendarData();
  }

  public function nextPeriod(): void
  {
    match ($this->view) {
      'month' => $this->currentDate = $this->currentDate->addMonth(),
      'week' => $this->currentDate = $this->currentDate->addWeek(),
      'day' => $this->currentDate = $this->currentDate->addDay(),
    };
    $this->loadCalendarData();
  }

  public function goToToday(): void
  {
    $this->currentDate = Carbon::today();
    $this->loadCalendarData();
  }

  public function setView(string $view): void
  {
    $this->view = $view;
    $this->loadCalendarData();
  }

  public function selectDate(string $date): void
  {
    $this->currentDate = Carbon::parse($date);
    if ($this->view !== 'day') {
      $this->view = 'day';
    }
    $this->loadCalendarData();
  }

  #[On('appointmentUpdated')]
  #[On('appointmentCreated')]
  #[On('appointmentDeleted')]
  public function refreshCalendar(): void
  {
    $this->clearCache();
    $this->loadCalendarData();
  }

  private function loadCalendarData(): void
  {
    $this->isLoading = true;
    try {
      $this->clearCache();
      $cacheKey = $this->generateCacheKey();
      $this->calendarData = Cache::remember($cacheKey, now()->addMinutes(5), function () {
        return match ($this->view) {
          'month' => $this->generateMonthData(),
          'week' => $this->generateWeekData(),
          'day' => $this->generateDayData(),
        };
      });
    } catch (\Exception $e) {
      \Illuminate\Support\Facades\Log::error('Calendar data loading error: ' . $e->getMessage());
      $this->calendarData = [];
    } finally {
      $this->isLoading = false;
    }
  }

  private function generateMonthData(): array
  {
    $startOfMonth = $this->currentDate->copy()->startOfMonth()->startOfWeek(CarbonInterface::SUNDAY);
    $endOfMonth = $this->currentDate->copy()->endOfMonth()->endOfWeek(CarbonInterface::SATURDAY);
    $weeks = [];
    $current = $startOfMonth->copy();
    while ($current <= $endOfMonth) {
      $week = [];
      for ($i = 1; $i <= 7; $i++) {
        $dayData = [
          'date' => $current->copy(),
          'isCurrentMonth' => $current->month === $this->currentDate->month,
          'isToday' => $current->isToday(),
          'isPast' => $current->isPast(),
          'isWeekend' => $current->isWeekend(),
          'appointments' => $this->getAppointmentsForDate($current)
        ];
        $week[] = $dayData;
        $current->addDay();
      }
      $weeks[] = $week;
    }
    return [
      'weeks' => $weeks,
      'title' => $this->currentDate->format('F Y')
    ];
  }

  private function generateWeekData(): array
  {
    $startOfWeek = $this->currentDate->copy()->startOfWeek();
    $endOfWeek = $this->currentDate->copy()->endOfWeek();
    $days = [];
    $current = $startOfWeek->copy();
    while ($current <= $endOfWeek) {
      $days[] = [
        'date' => $current->copy(),
        'isToday' => $current->isToday(),
        'isPast' => $current->isPast(),
        'isWeekend' => $current->isWeekend(),
        'appointments' => $this->getAppointmentsForDate($current)
      ];
      $current->addDay();
    }
    return [
      'days' => $days,
      'title' => $startOfWeek->format('M j') . ' - ' . $endOfWeek->format('M j, Y'),
      'timeSlots' => $this->generateTimeSlots()
    ];
  }

  private function generateDayData(): array
  {
    return [
      'date' => $this->currentDate->copy(),
      'title' => $this->currentDate->format('l, F j, Y'),
      'appointments' => $this->getAppointmentsForDate($this->currentDate),
      'timeSlots' => $this->generateTimeSlots()
    ];
  }

  private function getAppointmentsForDate(Carbon $date): Collection
  {
    $query = Appointments::with(['user', 'office', 'service', 'appointmentDetails'])
      ->whereDate('booking_date', $date->format('Y-m-d'));

    // Map office slugs to their corresponding staff roles
    $officeRoleMap = [
      'municipal-civil-registrar' => 'MCR-staff',
      'municipal-treasurers-office' => 'MTO-staff',
      'business-permits-and-licensing-section' => 'BPLS-staff',
    ];

    // Get all office IDs and their slugs
    $offices = Offices::whereIn('slug', array_keys($officeRoleMap))
      ->pluck('id', 'slug')
      ->toArray();

    if (auth()->check()) {
      $user = auth()->user();

      if ($user->hasRole('client')) {
        // Clients see only their own appointments
        $query->where('user_id', $user->id);
      } else {
        // For each office, if user has the corresponding staff role, filter by that office
        $officeIdsForUser = [];
        foreach ($officeRoleMap as $slug => $role) {
          if ($user->hasRole($role) && isset($offices[$slug])) {
            $officeIdsForUser[] = $offices[$slug];
          }
        }
        if (!empty($officeIdsForUser)) {
          $query->whereIn('office_id', $officeIdsForUser);
        }
      }
    }

    if ($this->selectedOfficeId) {
      $query->where('office_id', $this->selectedOfficeId);
    }

    return $query->orderBy('booking_time')->get();
  }

  private function generateTimeSlots(): array
  {
    $slots = [];
    $current = Carbon::parse('08:00');
    $end = Carbon::parse('17:00');
    while ($current < $end) {
      $slots[] = [
        'time' => $current->format('H:i'),
        'display' => $current->format('g:i A')
      ];
      $current->addMinutes(30);
    }
    return $slots;
  }

  private function generateCacheKey(): string
  {
    return sprintf(
      'calendar_%s_%s_%s',
      $this->view,
      $this->currentDate->format('Y-m-d'),
      $this->selectedOfficeId ?? 'all',
    );
  }

  private function clearCache(): void
  {
    $pattern = sprintf(
      'calendar_%s_%s_*',
      $this->view,
      $this->currentDate->format('Y-m-d')
    );
    Cache::flush(); // Simplified for this example
  }

  public function with(): array
  {
    return [
      'offices' => Offices::select('id', 'name')->get(),
      'staff' => User::role([
        'MCR-staff',
        'MTO-staff',
        'admin',
        'super-admin'
      ])
        ->select('id', 'first_name', 'last_name')
        ->get()
    ];
  }
}; ?>

<div class="full-calendar-container bg-white rounded-lg shadow"
  x-data="{ isLoading: @entangle('isLoading'), view: @entangle('view') }">
  <!-- Calendar Header -->
  <div class="border-b border-gray-200 p-4">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
      <!-- Navigation and Title -->
      <div class="flex items-center gap-4">
        <div class="flex items-center gap-2">
          <button wire:click="previousPeriod" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <h2 class="text-xl font-semibold text-gray-900 min-w-0">
            {{ $calendarData['title'] ?? '' }}
          </h2>
          <button wire:click="nextPeriod" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>
        <button wire:click="goToToday"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Today</button>
      </div>
      <!-- View Switcher -->
      <div class="flex items-center gap-2">
        <div class="inline-flex rounded-lg border border-gray-200 p-1">
          <button wire:click="setView('month')"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $view === 'month' ? 'bg-blue-100 text-blue-700' : 'text-gray-500 hover:text-gray-700' }}">Month</button>
            @hasrole('admin|super-admin|MCR-staff|MTO-staff|BPLS-staff')
          <button wire:click="setView('week')"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $view === 'week' ? 'bg-blue-100 text-blue-700' : 'text-gray-500 hover:text-gray-700' }}">Week</button>
          <button wire:click="setView('day')"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $view === 'day' ? 'bg-blue-100 text-blue-700' : 'text-gray-500 hover:text-gray-700' }}">Day</button>
            @endhasrole
        </div>
        <button wire:click="refreshCalendar"
          class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
        </button>
      </div>
    </div>
    <!-- Filters -->
    @hasrole('admin|super-admin')
    <div class="mt-4 pt-4 border-t border-gray-100">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Office Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Office</label>
          <select wire:model.live="selectedOfficeId"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">All Offices</option>
            @foreach($offices as $office)
        <option value="{{ $office->id }}">{{ $office->name }}</option>
      @endforeach
          </select>
        </div>

        <!-- Status Filters -->
        <!-- Removed status filter UI -->
      </div>
    </div>
    @endhasrole
  </div>
  <!-- Calendar Body -->
  <div class="calendar-body" wire:loading.class="opacity-50">
    @if($view === 'month')
    @include('livewire.components.calendar.month-view')
  @elseif($view === 'week')
    @include('livewire.components.calendar.week-view')
  @else
    @include('livewire.components.calendar.day-view')
  @endif
  </div>
  <!-- Loading Overlay -->
  <div wire:loading.delay class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center">
    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
  </div>
</div>