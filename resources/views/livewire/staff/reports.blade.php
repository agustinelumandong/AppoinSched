<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use App\Models\Appointments;
use App\Models\DocumentRequest;
use App\Models\Offices;
use App\Models\Services;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;

new class extends Component {
    use WithPagination;

    #[On('pdf-export-ready')]
    public function handlePdfExport($data): void
    {
        // Redirect to PDF generation route
        $this->redirect(route('reports.export.pdf'));
    }

    public ?string $selectedType = 'appointments';
    public ?int $selectedOfficeId = null;
    public ?int $selectedServiceId = null;
    public ?string $selectedStatus = null;
    public string $periodType = 'daily';
    public string $startDate;
    public string $endDate;
    public bool $isCustomRange = false;
    public array $statusOptions = [];
    public array $documentStatusOptions = ['pending', 'in-progress', 'cancelled'];
    public array $appointmentStatusOptions = ['on-going', 'cancelled', 'completed'];
    public array $paymentStatusOptions = ['unpaid', 'paid', 'failed'];
    public $reportData;

    // Export related properties
    public array $selectedStatuses = [];
    public array $selectedPaymentStatuses = [];
    public string $exportType = '';
    public bool $includeUserDetails = true;
    public bool $includeServiceDetails = true;
    public bool $isExporting = false;

    public function mount(): void
    {
        if (
            !auth()
                ->user()
                ->hasAnyRole(['MCR-staff', 'MTO-staff', 'BPLS-staff', 'MCR-admin', 'MTO-admin', 'BPLS-admin', 'admin', 'super-admin'])
        ) {
            abort(403, 'Unauthorized access');
        }
        $this->selectedOfficeId = $this->getOfficeIdForStaff();
        $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
        $this->updateStatusOptions();
        $this->generateReport();
    }

    public function getOfficeIdForStaff(): ?int
    {
        return auth()->user()->getOfficeIdForStaff();
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
        $this->updateStatusOptions();
        $this->generateReport();
    }

    protected function updateStatusOptions(): void
    {
        if ($this->selectedType === 'appointments') {
            $this->statusOptions = $this->appointmentStatusOptions;
        } else {
            $this->statusOptions = $this->documentStatusOptions;
        }
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
        $user = auth()->user();

        // Initialize reportData as an empty array if no results are found
        $this->reportData = [];

        if ($this->selectedType === 'appointments') {
            $query = Appointments::with(['office', 'user']);

            // Super-admin sees all offices, others see only their assigned office
            if (!$user->hasRole('super-admin') && $officeId) {
                $query->where('office_id', $officeId);
            }

            $query->when($status, fn($q) => $q->where('status', $status));
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
            $query = DocumentRequest::with(['office', 'service', 'user']);

            // Super-admin sees all offices, others see only their assigned office
            if (!$user->hasRole('super-admin') && $officeId) {
                $query->where('office_id', $officeId);
            }

            $query->when($serviceId, fn($q) => $q->where('service_id', $serviceId))
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

    public function showExportModal(string $type): void
    {
        $this->exportType = $type;
        // Initialize with all statuses if none selected
        $this->selectedStatuses = $this->selectedStatus ? [$this->selectedStatus] : $this->statusOptions;
        // Initialize payment statuses for document requests
        if ($this->selectedType === 'documents') {
            $this->selectedPaymentStatuses = $this->paymentStatusOptions;
        } else {
            $this->selectedPaymentStatuses = [];
        }
        $this->dispatch('open-modal-export-options');
    }

    public function exportToCsv(): void
    {
        $this->showExportModal('csv');
    }

    public function exportToPdf(): void
    {
        $this->showExportModal('pdf');
    }

    public function processExport(): void
    {
        $this->isExporting = true;

        // Collect data based on selected filters and statuses
        $data = $this->collectExportData();

        if ($this->exportType === 'csv') {
            $this->generateCsvExport($data);
        } else {
            $this->generatePdfExport($data);
        }
    }

    protected function collectExportData(): array
    {
        $officeId = $this->selectedOfficeId;
        $serviceId = $this->selectedServiceId;
        $period = $this->periodType !== 'custom' ? $this->periodType : null;
        $startDate = $this->isCustomRange ? $this->startDate : null;
        $endDate = $this->isCustomRange ? $this->endDate : null;
        $user = auth()->user();

        $exportData = [];

        if ($this->selectedType === 'appointments') {
            $query = Appointments::with(['office', 'user', 'appointmentDetails']);

            // Super-admin sees all offices, others see only their assigned office
            if (!$user->hasRole('super-admin') && $officeId) {
                $query->where('office_id', $officeId);
            }

            $query->when(
                    !empty($this->selectedStatuses),
                    function ($q) {
                        return $q->whereIn('status', $this->selectedStatuses);
                    },
                    function ($q) {
                        // If no statuses selected, don't filter by status
                        return $q;
                    },
                );

            $query = $this->applyDateFilters($query, $period, $startDate, $endDate, 'booking_date');
            $results = $query->get();

            foreach ($results as $appointment) {
                $item = [
                    'reference' => $appointment->reference_number,
                    'date' => $appointment->booking_date->format('Y-m-d'),
                    'time' => $appointment->booking_time,
                    'status' => ucfirst($appointment->status),
                    'purpose' => $appointment->purpose,
                    'office' => $appointment->office->name ?? 'N/A',
                ];

                if ($this->includeUserDetails) {
                    $item['user_name'] = $appointment->user->first_name . ' ' . $appointment->user->last_name;
                    $item['user_email'] = $appointment->user->email;
                    $item['user_phone'] = $appointment->user->phone ?? 'N/A';
                }

                $exportData[] = $item;
            }
        } else {
            $query = DocumentRequest::with(['office', 'service', 'user', 'details']);

            // Super-admin sees all offices, others see only their assigned office
            if (!$user->hasRole('super-admin') && $officeId) {
                $query->where('office_id', $officeId);
            }

            $query->when($serviceId, fn($q) => $q->where('service_id', $serviceId))
                ->when(
                    !empty($this->selectedStatuses),
                    function ($q) {
                        return $q->whereIn('status', $this->selectedStatuses);
                    },
                    function ($q) {
                        // If no statuses selected, don't filter by status
                        return $q;
                    },
                )
                ->when(
                    !empty($this->selectedPaymentStatuses),
                    function ($q) {
                        return $q->whereIn('payment_status', $this->selectedPaymentStatuses);
                    },
                    function ($q) {
                        // If no payment statuses selected, don't filter by payment status
                        return $q;
                    },
                );

            $query = $this->applyDateFilters($query, $period, $startDate, $endDate, 'created_at');
            $results = $query->get();

            foreach ($results as $request) {
                $item = [
                    'reference' => $request->reference_number,
                    'date' => $request->created_at->format('Y-m-d'),
                    'status' => ucfirst($request->status),
                    'purpose' => $request->purpose,
                    'office' => $request->office->name ?? 'N/A',
                ];

                if ($this->includeServiceDetails) {
                    $item['service'] = $request->service->title ?? 'N/A';
                    $item['payment_status'] = ucfirst($request->payment_status ?? 'N/A');
                }

                if ($this->includeUserDetails) {
                    $item['user_name'] = $request->user->first_name . ' ' . $request->user->last_name;
                    $item['user_email'] = $request->user->email;
                    $item['user_phone'] = $request->user->phone ?? 'N/A';
                }

                $exportData[] = $item;
            }
        }

        return $exportData;
    }

    protected function generateCsvExport(array $data): void
    {
        if (empty($data)) {
            session()->flash('error', 'No data available to export.');
            $this->isExporting = false;
            return;
        }

        $filename = $this->selectedType . '_report_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = array_keys($data[0]);

        $csvContent = '';
        // Add BOM to fix UTF-8 in Excel
        $csvContent .= "\xEF\xBB\xBF";

        // Add headers
        $csvContent .=
            implode(
                ',',
                array_map(function ($header) {
                    return '"' . ucfirst(str_replace('_', ' ', $header)) . '"';
                }, $headers),
            ) . "\n";

        // Add data rows
        foreach ($data as $row) {
            $csvContent .=
                implode(
                    ',',
                    array_map(function ($value) {
                        return '"' . str_replace('"', '""', $value) . '"';
                    }, $row),
                ) . "\n";
        }

        $this->isExporting = false;

        // Store CSV content in session for download
        session(['csv_export_content' => $csvContent, 'csv_export_filename' => $filename]);

        // Trigger download via JavaScript
        $this->dispatch('csv-download-ready', ['filename' => $filename]);

        session()->flash('success', 'CSV export has been generated successfully.');
    }

    protected function generatePdfExport(array $data): void
    {
        if (empty($data)) {
            session()->flash('error', 'No data available to export.');
            $this->isExporting = false;
            return;
        }

        $filename = $this->selectedType . '_report_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        $officeInfo = $this->getOfficeInfo();
        $reportTitle = ucfirst($this->selectedType) . ' Report';
        $reportPeriod = $this->getReportPeriodText();

        // Generate PDF in a separate request to avoid memory issues with Livewire
        $exportParams = [
            'data' => $data,
            'officeInfo' => $officeInfo,
            'reportTitle' => $reportTitle,
            'reportPeriod' => $reportPeriod,
            'filename' => $filename,
        ];

        // Store export parameters in session
        session(['pdf_export_params' => $exportParams]);

        $this->isExporting = false;

        // Redirect to a dedicated route for PDF generation
        $this->dispatch('pdf-export-ready', ['filename' => $filename]);

        session()->flash('success', 'PDF export has been generated successfully.');
    }

    protected function getOfficeInfo(): array
    {
        $office = Offices::find($this->selectedOfficeId);
        return [
            'name' => $office->name ?? 'All Offices',
            'address' => $office->address ?? '',
            'contact' => $office->contact ?? '',
        ];
    }

    protected function getReportPeriodText(): string
    {
        if ($this->isCustomRange) {
            return 'From ' . Carbon::parse($this->startDate)->format('M d, Y') . ' to ' . Carbon::parse($this->endDate)->format('M d, Y');
        }

        return match ($this->periodType) {
            'daily' => 'Daily Report - ' . Carbon::today()->format('M d, Y'),
            'weekly' => 'Weekly Report - ' . Carbon::now()->startOfWeek()->format('M d, Y') . ' to ' . Carbon::now()->endOfWeek()->format('M d, Y'),
            'monthly' => 'Monthly Report - ' . Carbon::now()->format('F Y'),
            default => 'Custom Period Report',
        };
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

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('pdf-export-ready', (data) => {
                // Open PDF in new window
                window.open('{{ route('reports.export.pdf') }}', '_blank');
            });

            Livewire.on('csv-download-ready', (data) => {
                // Open CSV download in new window
                window.open('{{ route('reports.export.csv') }}', '_blank');
            });
        });
    </script>
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
                    <span wire:loading.remove wire:target="exportToCsv">
                        <i class="bi bi-file-earmark-spreadsheet mr-1"></i> Export to CSV
                    </span>
                    <span wire:loading wire:target="exportToCsv">
                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                        Loading...
                    </span>
                </button>
                <button type="button" wire:click="exportToPdf" class="flux-btn flux-btn-danger text-sm"
                    style="font-size: 12px;">
                    <span wire:loading.remove wire:target="exportToPdf">
                        <i class="bi bi-file-earmark-pdf mr-1"></i> Export to PDF
                    </span>
                    <span wire:loading wire:target="exportToPdf">
                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                        Loading...
                    </span>
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
                                        <span
                                            class="flux-badge {{ match ($status) {
                                                // Document request statuses
                                                'pending' => 'flux-badge-warning',
                                                'in-progress' => 'flux-badge-info',
                                                'cancelled' => 'flux-badge-danger',
                                                // Appointment statuses
                                                'on-going' => 'flux-badge-warning',
                                                // Payment statuses
                                                'paid' => 'flux-badge-success',
                                                'unpaid' => 'flux-badge-danger',
                                                'failed' => 'flux-badge-danger',
                                                default => 'flux-badge-warning',
                                            } }}">
                                            {{ ucfirst(str_replace('-', ' ', $status)) }}
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

    <!-- Export Options Modal -->
    <x-modal id="export-options" title="Export Options" size="max-w-2xl">
        <div class="modal-body">
            <div class="text-sm space-y-4 max-h-[60vh] overflow-y-auto p-2">
                <h3 class="font-bold text-lg mb-2">Select Export Options</h3>

                <div class="bg-base-200 p-3 rounded-lg">
                    <p>Please select which statuses you want to include in your
                        {{ $exportType === 'csv' ? 'CSV' : 'PDF' }} export.</p>
                </div>

                <div class="mt-4">
                    <h4 class="font-semibold text-base mb-2">Status Filters</h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach ($statusOptions as $status)
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" wire:model.live="selectedStatuses" value="{{ $status }}"
                                    class="checkbox checkbox-sm">
                                <span>{{ ucfirst($status) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                @if ($selectedType === 'documents')
                    <div class="mt-4">
                        <h4 class="font-semibold text-base mb-2">Payment Status Filters</h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            @foreach ($paymentStatusOptions as $status)
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" wire:model.live="selectedPaymentStatuses"
                                        value="{{ $status }}" class="checkbox checkbox-sm">
                                    <span>{{ ucfirst($status) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mt-4">
                    <h4 class="font-semibold text-base mb-2">Include Additional Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" wire:model.live="includeUserDetails" class="checkbox checkbox-sm">
                            <span>User Details</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" wire:model.live="includeServiceDetails"
                                class="checkbox checkbox-sm">
                            <span>Service Details</span>
                        </label>
                    </div>
                </div>

                <div class="bg-base-200 p-3 rounded-lg mt-4">
                    <p class="text-sm">The export will include data based on your current filters:</p>
                    <ul class="list-disc list-inside text-sm mt-2">
                        <li>Type: {{ ucfirst($selectedType) }}</li>
                        <li>Period: {{ ucfirst($periodType) }}</li>
                        @if ($isCustomRange)
                            <li>Date Range: {{ $startDate }} to {{ $endDate }}</li>
                        @endif
                        @if ($selectedServiceId)
                            <li>Service:
                                {{ collect($services)->firstWhere('id', $selectedServiceId)?->title ?? 'N/A' }}</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <x-slot name="footer">
            <div class="gap-2">
                <button type="button" class="flux-btn flux-btn-outline" x-data
                    x-on:click="$dispatch('close-modal-export-options')">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>
                <button type="button" class="flux-btn btn-sm flux-btn-success" x-data="{}"
                    x-on:click="
                    $dispatch('close-modal-export-options');
                    $wire.processExport();
                ">
                    <span wire:loading.remove wire:target="processExport">
                        <i class="bi bi-check-circle me-1"></i>
                        Export {{ $exportType === 'csv' ? 'CSV' : 'PDF' }}
                    </span>
                    <span wire:loading wire:target="processExport">
                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                        Generating...
                    </span>
                </button>
            </div>
        </x-slot>
    </x-modal>
</div>
