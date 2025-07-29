<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use App\Models\Appointments;
use App\Models\DocumentRequest;
use App\Models\Offices;
use App\Models\Services;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Collection;

new class extends Component {
    use WithPagination;

    public ?string $selectedType = 'appointments';
    public ?int $selectedOfficeId = null;
    public ?int $selectedServiceId = null;
    public ?string $selectedStatus = null;
    public string $periodType = 'daily';
    public string $startDate;
    public string $endDate;
    public bool $isCustomRange = false;
    public array $statusOptions = ['pending', 'approved', 'completed', 'cancelled', 'rejected', 'in-progress', 'ready-for-pickup'];
    public $reportData;

    public function mount(): void
    {
        if (
            !auth()
                ->user()
                ->hasAnyRole(['MCR-staff', 'MTO-staff', 'BPLS-staff', 'admin', 'super-admin'])
        ) {
            abort(403, 'Unauthorized access');
        }
        $this->selectedOfficeId = $this->getOfficeIdForStaff();
        $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
        $this->generateReport();
    }

    public function getOfficeIdForStaff(): ?int
    {
        if (auth()->user()->hasRole('MCR-staff')) {
            return Offices::where('slug', 'municipal-civil-registrar')->value('id');
        }
        if (auth()->user()->hasRole('MTO-staff')) {
            return Offices::where('slug', 'municipal-treasurers-office')->value('id');
        }
        if (auth()->user()->hasRole('BPLS-staff')) {
            return Offices::where('slug', 'business-permits-and-licensing-section')->value('id');
        }
        return null;
    }

    public function updatedPeriodType(): void
    {
        $this->isCustomRange = $this->periodType === 'custom';
        $this->generateReport();
    }

    public function updatedSelectedServiceId(): void
    {
        $this->generateReport();
    }

    public function updatedSelectedStatus(): void
    {
        $this->generateReport();
    }

    public function updatedSelectedType(): void
    {
        $this->generateReport();
    }

    public function applyDateFilter(): void
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ]);
        $this->generateReport();
    }

    public function generateReport(): void
    {
        $officeId = $this->selectedOfficeId;
        $serviceId = $this->selectedServiceId;
        $status = $this->selectedStatus;
        $period = $this->periodType !== 'custom' ? $this->periodType : null;
        $startDate = $this->isCustomRange ? $this->startDate : null;
        $endDate = $this->isCustomRange ? $this->endDate : null;

        // Initialize reportData as an empty array if no results are found
        $this->reportData = [];

        if ($this->selectedType === 'appointments') {
            $query = Appointments::with(['office', 'user'])
                ->where('office_id', $officeId)
                ->when($status, fn($q) => $q->where('status', $status));
            $query = $this->applyDateFilters($query, $period, $startDate, $endDate, 'booking_date');
            $results = $query->get();

            if ($results->isNotEmpty()) {
                $this->reportData = $results
                    ->groupBy(function ($item) use ($period) {
                        if ($period === 'daily') {
                            return $item->booking_date->format('Y-m-d');
                        } elseif ($period === 'weekly') {
                            return $item->booking_date->format('Y-W');
                        } elseif ($period === 'monthly') {
                            return $item->booking_date->format('Y-m');
                        }
                        return 'all';
                    })
                    ->toArray();
            }
        } else {
            $query = DocumentRequest::with(['office', 'service', 'user'])
                ->where('office_id', $officeId)
                ->when($serviceId, fn($q) => $q->where('service_id', $serviceId))
                ->when($status, fn($q) => $q->where('status', $status));
            $query = $this->applyDateFilters($query, $period, $startDate, $endDate, 'created_at');
            $results = $query->get();

            if ($results->isNotEmpty()) {
                $this->reportData = $results
                    ->groupBy(function ($item) use ($period) {
                        if ($period === 'daily') {
                            return $item->created_at->format('Y-m-d');
                        } elseif ($period === 'weekly') {
                            return $item->created_at->format('Y-W');
                        } elseif ($period === 'monthly') {
                            return $item->created_at->format('Y-m');
                        }
                        return 'all';
                    })
                    ->toArray();
            }
        }
    }

    private function applyDateFilters($query, ?string $period, ?string $startDate, ?string $endDate, string $dateField)
    {
        if ($startDate && $endDate) {
            return $query->whereBetween($dateField, [$startDate, $endDate]);
        }
        if ($period === 'daily') {
            return $query->whereDate($dateField, Carbon::today());
        }
        if ($period === 'weekly') {
            return $query->whereBetween($dateField, [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        }
        if ($period === 'monthly') {
            return $query->whereBetween($dateField, [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
        }
        return $query;
    }

    public function exportToCsv(): void
    {
        // TODO: Implement CSV export
    }

    public function exportToPdf(): void
    {
        // TODO: Implement PDF export
    }

    public function with(): array
    {
        $officeId = $this->getOfficeIdForStaff();
        return [
            'offices' => Offices::where('id', $officeId)->get(),
            'services' => Services::where('office_id', $officeId)->get()->all(),
        ];
    }
}; ?>

<div>
    @include('components.alert')
    <div class="flux-card p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Staff Reports</h1>
                <p class="text-gray-600 mt-1">Generate and export reports for your assigned office</p>
            </div>
        </div>
    </div>
    <div class="flux-card p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select wire:model.live="selectedType" class="form-select">
                    <option value="appointments">Appointments</option>
                    <option value="documents">Document Requests</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Service</label>
                <select wire:model.live="selectedServiceId" class="form-select">
                    <option value="">All Services</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}">{{ $service->title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model.live="selectedStatus" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach ($statusOptions as $status)
                        <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Period</label>
                <select wire:model.live="periodType" class="form-select">
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>
            <div class="mt-4 flex gap-2">
                <button type="button" wire:click="exportToCsv" class="flux-btn flux-btn-success text-sm"
                    style="font-size: 12px;">
                    <i class="bi bi-file-earmark-spreadsheet mr-1"></i> Export to CSV
                </button>
                <button type="button" wire:click="exportToPdf" class="flux-btn flux-btn-danger text-sm"
                    style="font-size: 12px;">
                    <i class="bi bi-file-earmark-pdf mr-1"></i> Export to PDF
                </button>
            </div>
        </div>
        @if ($isCustomRange)
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" wire:model="startDate" class="form-input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" wire:model="endDate" class="form-input">
                </div>
                <div>
                    <button type="button" wire:click="applyDateFilter" class="flux-btn flux-btn-primary">
                        Apply
                    </button>
                </div>
            </div>
        @endif

    </div>
    <div class="flux-card p-6">
        <div class="overflow-x-auto">
            <table class="flux-table w-full">
                <thead>
                    <tr>
                        <th>Period</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $period => $items)
                        @php
                            $groupedByService = collect($items)->groupBy(function ($item) {
                                return isset($item['service']) && isset($item['service']['title'])
                                    ? $item['service']['title']
                                    : 'N/A';
                            });
                        @endphp
                        @foreach ($groupedByService as $serviceName => $serviceItems)
                            @php
                                $groupedByStatus = collect($serviceItems)->groupBy('status');
                            @endphp
                            @foreach ($groupedByStatus as $status => $statusItems)
                                        <tr>
                                            <td>{{ $period }}</td>
                                            <td>{{ $serviceName ?? 'N/A' }}</td>
                                            <td>
                                                <span class="flux-badge {{ match ($status) {
                                    'pending' => 'flux-badge-warning',
                                    'approved' => 'flux-badge-success',
                                    'completed' => 'flux-badge-info',
                                    'cancelled' => 'flux-badge-danger',
                                    'rejected' => 'flux-badge-danger',
                                    'in-progress' => 'flux-badge-info',
                                    'ready-for-pickup' => 'flux-badge-success',
                                    'paid' => 'flux-badge-success',
                                    'unpaid' => 'flux-badge-danger',
                                    'On-going' => 'flux-badge-warning',
                                    default => 'flux-badge-warning',
                                } }}">
                                                    {{ ucfirst($status) }}
                                                </span>
                                            </td>
                                            <td>{{ $statusItems->count() }}</td>
                                        </tr>
                            @endforeach
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-8">
                                <div class="text-gray-500">
                                    <i class="bi bi-bar-chart text-4xl mb-2"></i>
                                    <p>No report data available for the selected filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>