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

new class extends Component {
    use WithPagination;

    public ?string $selectedType = 'appointments';
    public ?int $selectedOfficeId = null;
    public ?int $selectedServiceId = null;
    public string $periodType = 'monthly';
    public string $startDate;
    public string $endDate;
    public bool $isCustomRange = false;

    // Analytics data
    public array $analyticsData = [];
    public array $documentRequestsAnalytics = [];
    public array $appointmentsAnalytics = [];
    public array $performanceAnalysis = [];
    public array $overallSummary = [];
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

        $user = auth()->user();

        // Set initial office - admins/super-admins can see all, staff see their assigned office
        if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
            $this->selectedOfficeId = null; // null means all offices
        } else {
            $this->selectedOfficeId = $this->getOfficeIdForStaff();
        }

        // Set default to current month
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->periodType = 'monthly';

        $this->generateAnalytics();
    }

    public function getOfficeIdForStaff(): ?int
    {
        return auth()->user()->getOfficeIdForStaff();
    }

    public function updatedPeriodType(): void
    {
        $this->isCustomRange = $this->periodType === 'custom';

        if (!$this->isCustomRange) {
            $this->updateDatesForPeriod();
        }

        $this->generateAnalytics();
    }

    public function updatedSelectedServiceId(): void
    {
        $this->generateAnalytics();
    }

    public function updatedSelectedType(): void
    {
        $this->generateAnalytics();
    }

    public function updatedSelectedOfficeId(): void
    {
        $this->generateAnalytics();
    }

    protected function updateDatesForPeriod(): void
    {
        match ($this->periodType) {
            'daily' => [
                $this->startDate = Carbon::today()->format('Y-m-d'),
                $this->endDate = Carbon::today()->format('Y-m-d'),
            ],
            'weekly' => [
                $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d'),
                $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d'),
            ],
            'monthly' => [
                $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d'),
                $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d'),
            ],
            default => null,
        };
    }

    public function applyDateFilter(): void
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ]);
        $this->generateAnalytics();
    }

    public function generateAnalytics(): void
    {
        $user = auth()->user();
        $officeId = $this->selectedOfficeId;
        $serviceId = $this->selectedServiceId;
        $startDate = Carbon::parse($this->startDate)->startOfDay();
        $endDate = Carbon::parse($this->endDate)->endOfDay();

        // Reset analytics data
        $this->documentRequestsAnalytics = [];
        $this->appointmentsAnalytics = [];
        $this->performanceAnalysis = [];
        $this->overallSummary = [];

        // Determine which offices to query
        $officeIds = $this->getOfficeIdsToQuery($user, $officeId);

        // Generate document requests analytics
        $this->documentRequestsAnalytics = $this->calculateDocumentRequestsAnalytics($officeIds, $serviceId, $startDate, $endDate);

        // Generate appointments analytics
        $this->appointmentsAnalytics = $this->calculateAppointmentsAnalytics($officeIds, $startDate, $endDate);

        // Generate performance analysis
        $this->performanceAnalysis = $this->calculatePerformanceAnalysis($officeIds, $serviceId, $startDate, $endDate);

        // Generate overall summary
        $this->overallSummary = $this->calculateOverallSummary($officeIds, $startDate, $endDate);
    }

    public function getOfficeIdsToQuery($user, ?int $selectedOfficeId): array
    {
        // Super-admin or admin with no office selected = all offices
        if (($user->hasRole('super-admin') || $user->hasRole('admin')) && $selectedOfficeId === null) {
            return Offices::pluck('id')->toArray();
        }

        // Admin with specific office selected
        if (($user->hasRole('super-admin') || $user->hasRole('admin')) && $selectedOfficeId !== null) {
            return [$selectedOfficeId];
        }

        // Staff members - only their assigned office
        $officeId = $this->getOfficeIdForStaff();
        return $officeId ? [$officeId] : [];
    }

    protected function calculateDocumentRequestsAnalytics(array $officeIds, ?int $serviceId, Carbon $startDate, Carbon $endDate): array
    {
        $query = DocumentRequest::with(['office', 'service'])
            ->whereIn('office_id', $officeIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['complete', 'cancelled']);

        if ($serviceId) {
            $query->where('service_id', $serviceId);
        }

        $results = $query->get();

        // Group by service/document type
        $byService = $results->groupBy('service_id');
        $analytics = [];

        foreach ($byService as $serviceId => $requests) {
            $service = $requests->first()->service;
            $completed = $requests->where('status', 'complete')->count();
            $cancelled = $requests->where('status', 'cancelled')->count();
            $total = $completed + $cancelled;
            $completionRate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;

            $analytics[] = [
                'document_type' => $service->title ?? 'N/A',
                'completed' => $completed,
                'cancelled' => $cancelled,
                'completion_rate' => $completionRate,
                'total' => $total,
            ];
        }

        return $analytics;
    }

    protected function formatPurpose(string $purpose): string
    {
        if (empty($purpose)) {
            return 'N/A';
        }

        // Convert kebab-case to Title Case
        return ucwords(str_replace('-', ' ', $purpose));
    }

    protected function calculateAppointmentsAnalytics(array $officeIds, Carbon $startDate, Carbon $endDate): array
    {
        $query = Appointments::with(['office'])
            ->whereIn('office_id', $officeIds)
            ->whereBetween('booking_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereIn('status', ['completed', 'cancelled']);

        $results = $query->get();

        // Group by purpose
        $byPurpose = $results->groupBy('purpose');
        $analytics = [];

        foreach ($byPurpose as $purpose => $appointments) {
            $completed = $appointments->where('status', 'completed')->count();
            $cancelled = $appointments->where('status', 'cancelled')->count();
            $total = $completed + $cancelled;
            $completionRate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;

            $analytics[] = [
                'purpose' => $this->formatPurpose($purpose),
                'purpose_raw' => $purpose, // Keep raw for grouping
                'completed' => $completed,
                'cancelled' => $cancelled,
                'completion_rate' => $completionRate,
                'total' => $total,
            ];
        }

        return $analytics;
    }

    protected function calculatePerformanceAnalysis(array $officeIds, ?int $serviceId, Carbon $startDate, Carbon $endDate): array
    {
        $analysis = [
            'most_requested_documents' => null,
            'highest_completion_rate_purpose' => null,
            'increases' => [],
            'decreases' => [],
        ];

        // Get previous period for comparison
        $periodDays = $startDate->diffInDays($endDate) + 1;
        $previousStartDate = $startDate->copy()->subDays($periodDays);
        $previousEndDate = $startDate->copy()->subDay();

        // Most requested documents
        $docQuery = DocumentRequest::with(['service'])
            ->whereIn('office_id', $officeIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['complete', 'cancelled']);

        if ($serviceId) {
            $docQuery->where('service_id', $serviceId);
        }

        $docRequests = $docQuery->get();
        if ($docRequests->isNotEmpty()) {
            $byService = $docRequests->groupBy('service_id');
            $mostRequested = $byService->sortByDesc(fn($group) => $group->count())->first();
            if ($mostRequested) {
                $analysis['most_requested_documents'] = $mostRequested->first()->service->title ?? 'N/A';
            }
        }

        // Highest completion rate purpose
        $appQuery = Appointments::whereIn('office_id', $officeIds)
            ->whereBetween('booking_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereIn('status', ['completed', 'cancelled']);

        $appointments = $appQuery->get();
        if ($appointments->isNotEmpty()) {
            $byPurpose = $appointments->groupBy('purpose');
            $highestRate = 0;
            $highestPurpose = null;

            foreach ($byPurpose as $purpose => $apps) {
                $completed = $apps->where('status', 'completed')->count();
                $total = $apps->count();
                $rate = $total > 0 ? ($completed / $total) * 100 : 0;

                if ($rate > $highestRate) {
                    $highestRate = $rate;
                    $highestPurpose = $purpose ?: 'N/A';
                }
            }

            if ($highestPurpose) {
                $analysis['highest_completion_rate_purpose'] = $this->formatPurpose($highestPurpose) . ' (' . round($highestRate, 1) . '%)';
            }
        }

        // Period-over-period comparison for document requests
        $currentDocCount = DocumentRequest::whereIn('office_id', $officeIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['complete', 'cancelled'])
            ->count();

        $previousDocCount = DocumentRequest::whereIn('office_id', $officeIds)
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->whereIn('status', ['complete', 'cancelled'])
            ->count();

        if ($previousDocCount > 0) {
            $docChange = (($currentDocCount - $previousDocCount) / $previousDocCount) * 100;
            if ($docChange > 0) {
                $analysis['increases'][] = 'Document requests +' . round($docChange, 1) . '%';
            } elseif ($docChange < 0) {
                $analysis['decreases'][] = 'Document requests ' . round($docChange, 1) . '%';
            }
        }

        // Period-over-period comparison for appointments
        $currentAppCount = Appointments::whereIn('office_id', $officeIds)
            ->whereBetween('booking_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereIn('status', ['completed', 'cancelled'])
            ->count();

        $previousAppCount = Appointments::whereIn('office_id', $officeIds)
            ->whereBetween('booking_date', [$previousStartDate->format('Y-m-d'), $previousEndDate->format('Y-m-d')])
            ->whereIn('status', ['completed', 'cancelled'])
            ->count();

        if ($previousAppCount > 0) {
            $appChange = (($currentAppCount - $previousAppCount) / $previousAppCount) * 100;
            if ($appChange > 0) {
                $analysis['increases'][] = 'Appointments +' . round($appChange, 1) . '%';
            } elseif ($appChange < 0) {
                $analysis['decreases'][] = 'Appointments ' . round($appChange, 1) . '%';
            }
        }

        return $analysis;
    }

    protected function calculateOverallSummary(array $officeIds, Carbon $startDate, Carbon $endDate): array
    {
        $summary = [];

        // Get data per office
        foreach ($officeIds as $officeId) {
            $office = Offices::find($officeId);
            if (!$office) {
                continue;
            }

            // Document requests
            $docCompleted = DocumentRequest::where('office_id', $officeId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'complete')
                ->count();

            $docCancelled = DocumentRequest::where('office_id', $officeId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'cancelled')
                ->count();

            $docTotal = $docCompleted + $docCancelled;
            $docRate = $docTotal > 0 ? round(($docCompleted / $docTotal) * 100, 1) : 0;

            // Appointments
            $appCompleted = Appointments::where('office_id', $officeId)
                ->whereBetween('booking_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->where('status', 'completed')
                ->count();

            $appCancelled = Appointments::where('office_id', $officeId)
                ->whereBetween('booking_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->where('status', 'cancelled')
                ->count();

            $appTotal = $appCompleted + $appCancelled;
            $appRate = $appTotal > 0 ? round(($appCompleted / $appTotal) * 100, 1) : 0;

            // Overall for this office
            $totalCompleted = $docCompleted + $appCompleted;
            $totalCancelled = $docCancelled + $appCancelled;
            $totalTransactions = $totalCompleted + $totalCancelled;
            $overallRate = $totalTransactions > 0 ? round(($totalCompleted / $totalTransactions) * 100, 1) : 0;

            $summary[] = [
                'office' => $office->name,
                'completed' => $totalCompleted,
                'cancelled' => $totalCancelled,
                'total_transactions' => $totalTransactions,
                'completion_rate' => $overallRate,
            ];
        }

        // Calculate grand total if multiple offices
        if (count($summary) > 1) {
            $grandCompleted = array_sum(array_column($summary, 'completed'));
            $grandCancelled = array_sum(array_column($summary, 'cancelled'));
            $grandTotal = $grandCompleted + $grandCancelled;
            $grandRate = $grandTotal > 0 ? round(($grandCompleted / $grandTotal) * 100, 1) : 0;

            $summary['grand_total'] = [
                'office' => 'Grand Total',
                'completed' => $grandCompleted,
                'cancelled' => $grandCancelled,
                'total_transactions' => $grandTotal,
                'completion_rate' => $grandRate,
            ];
        }

        return $summary;
    }

    public function exportToPdf(): void
    {
        try {
            $this->isExporting = true;

            // Ensure analytics are generated
            $this->generateAnalytics();

            $user = auth()->user();
            $officeId = $this->selectedOfficeId;
            $officeIds = $this->getOfficeIdsToQuery($user, $officeId);

            // Get office info
            $officeInfo = [];
            if (count($officeIds) === 1) {
                $office = Offices::find($officeIds[0]);
                $officeInfo = [
                    'name' => $office->name ?? 'N/A',
                ];
            } else {
                $officeInfo = [
                    'name' => 'All Offices',
                ];
            }

            // Prepare analytics data for PDF
            $pdfData = [
                'document_requests' => $this->documentRequestsAnalytics,
                'appointments' => $this->appointmentsAnalytics,
                'performance_analysis' => $this->performanceAnalysis,
                'overall_summary' => $this->overallSummary,
                'office_info' => $officeInfo,
                'report_period' => $this->getReportPeriodText(),
                'generated_date' => now()->format('F d, Y'),
                'period_type' => $this->periodType,
                'selected_type' => $this->selectedType,
            ];

            // Store in session for PDF generation
            session(['pdf_export_params' => $pdfData]);
            session()->save(); // Ensure session is saved

            $this->isExporting = false;

            // Dispatch event to trigger PDF download
            $this->dispatch('trigger-pdf-download', url: route('reports.export.pdf'));
        } catch (\Exception $e) {
            $this->isExporting = false;
            session()->flash('error', 'Failed to prepare PDF export: ' . $e->getMessage());
        }
    }

    protected function getReportPeriodText(): string
    {
        if ($this->isCustomRange) {
            return 'From ' . Carbon::parse($this->startDate)->format('M d, Y') . ' to ' . Carbon::parse($this->endDate)->format('M d, Y');
        }

        return match ($this->periodType) {
            'daily' => Carbon::parse($this->startDate)->format('F d, Y'),
            'weekly' => 'Week of ' . Carbon::parse($this->startDate)->format('M d, Y') . ' to ' . Carbon::parse($this->endDate)->format('M d, Y'),
            'monthly' => Carbon::parse($this->startDate)->format('F Y'),
            default => 'Custom Period Report',
        };
    }

    public function with(): array
    {
        $user = auth()->user();
        $officeId = $this->getOfficeIdForStaff();

        // For admins/super-admins, show all offices; for staff, show only their office
        if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
            $offices = Offices::all();
        } else {
            $offices = Offices::where('id', $officeId)->get();
        }

        // Get services for selected office or all offices
        if ($this->selectedOfficeId) {
            $services = Services::where('office_id', $this->selectedOfficeId)->get();
        } else {
            $services = Services::all();
        }

        return [
            'offices' => $offices,
            'services' => $services,
        ];
    }
}; ?>

<div x-data x-on:trigger-pdf-download.window="setTimeout(() => window.open($event.detail.url, '_blank'), 150)">
    @include('components.alert')


    <div class="flux-card p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Service Reports</h1>
                <p class="text-gray-600 mt-1">Analytics and performance metrics for appointments and document requests</p>
            </div>
        </div>
    </div>

    <div class="flux-card p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select wire:model.live="selectedType" class="form-select">
                    <option value="appointments">Appointments</option>
                    <option value="documents">Document Requests</option>
                    <option value="both">Both</option>
                </select>
            </div>

            @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Office</label>
                <select wire:model.live="selectedOfficeId" class="form-select">
                    <option value="">All Offices</option>
                    @foreach ($offices as $office)
                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

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
                <label class="block text-sm font-medium text-gray-700 mb-1">Period</label>
                <select wire:model.live="periodType" class="form-select">
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>

            <div class="mt-4">
                <button type="button" wire:click="exportToPdf" class="flux-btn flux-btn-danger text-sm w-full"
                    style="font-size: 12px;">
                    <span wire:loading.remove wire:target="exportToPdf">
                        <i class="bi bi-file-earmark-pdf mr-1"></i> Export to PDF
                    </span>
                    <span wire:loading wire:target="exportToPdf">
                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                        Generating...
                    </span>
                </button>
            </div>
        </div>

        @if ($isCustomRange)
            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" wire:model="startDate" class="form-input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" wire:model="endDate" class="form-input">
                </div>
                <div class="flex items-end">
                    <button type="button" wire:click="applyDateFilter" class="flux-btn flux-btn-primary">
                        Apply
                    </button>
                </div>
            </div>
        @endif
    </div>

    @if(empty($documentRequestsAnalytics) && empty($appointmentsAnalytics))
        <div class="flux-card p-6">
            <div class="text-center py-8">
                <div class="text-gray-500">
                    <i class="bi bi-bar-chart text-4xl mb-2"></i>
                    <p>No report data available for the selected filters.</p>
                    <p class="text-sm mt-2">Only Completed and Cancelled transactions are included in reports.</p>
                </div>
            </div>
        </div>
    @else
        @php
            $user = auth()->user();
            $officeId = $this->selectedOfficeId ?? null;
            $officeIds = $this->getOfficeIdsToQuery($user, $officeId);
            $isMultiOffice = count($officeIds) > 1;
        @endphp

        <!-- Document Requests Section -->
        @if(($selectedType === 'documents' || $selectedType === 'both') && !empty($documentRequestsAnalytics))
            @php
                $office = $isMultiOffice ? null : Offices::find($officeIds[0] ?? null);
            @endphp
            <div class="flux-card p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">
                    {{ $isMultiOffice ? 'All Offices' : ($office->name ?? 'Office') }} - Document Requests
                </h2>

                <div class="overflow-x-auto mb-4">
                    <table class="flux-table w-full">
                        <thead>
                            <tr>
                                <th>Document Type</th>
                                <th>Completed</th>
                                <th>Cancelled</th>
                                <th>Completion Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documentRequestsAnalytics as $item)
                                <tr>
                                    <td>{{ $item['document_type'] }}</td>
                                    <td>{{ $item['completed'] }}</td>
                                    <td>{{ $item['cancelled'] }}</td>
                                    <td>{{ $item['completion_rate'] }}%</td>
                                </tr>
                            @endforeach
                            @if(!empty($documentRequestsAnalytics))
                                <tr class="font-bold">
                                    <td>Total Document Requests</td>
                                    <td>{{ array_sum(array_column($documentRequestsAnalytics, 'completed')) }}</td>
                                    <td>{{ array_sum(array_column($documentRequestsAnalytics, 'cancelled')) }}</td>
                                    <td>
                                        @php
                                            $totalCompleted = array_sum(array_column($documentRequestsAnalytics, 'completed'));
                                            $totalCancelled = array_sum(array_column($documentRequestsAnalytics, 'cancelled'));
                                            $total = $totalCompleted + $totalCancelled;
                                            $rate = $total > 0 ? round(($totalCompleted / $total) * 100, 1) : 0;
                                        @endphp
                                        {{ $rate }}%
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Appointments Section -->
        @if(($selectedType === 'appointments' || $selectedType === 'both') && !empty($appointmentsAnalytics))
            @php
                $office = $isMultiOffice ? null : Offices::find($officeIds[0] ?? null);
            @endphp
            <div class="flux-card p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">
                    {{ $isMultiOffice ? 'All Offices' : ($office->name ?? 'Office') }} - Appointments
                </h2>

                <div class="overflow-x-auto mb-4">
                    <table class="flux-table w-full">
                        <thead>
                            <tr>
                                <th>Purpose</th>
                                <th>Completed</th>
                                <th>Cancelled</th>
                                <th>Completion Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointmentsAnalytics as $item)
                                <tr>
                                    <td>{{ $item['purpose'] }}</td>
                                    <td>{{ $item['completed'] }}</td>
                                    <td>{{ $item['cancelled'] }}</td>
                                    <td>{{ $item['completion_rate'] }}%</td>
                                </tr>
                            @endforeach
                            @if(!empty($appointmentsAnalytics))
                                <tr class="font-bold">
                                    <td>Total Appointments</td>
                                    <td>{{ array_sum(array_column($appointmentsAnalytics, 'completed')) }}</td>
                                    <td>{{ array_sum(array_column($appointmentsAnalytics, 'cancelled')) }}</td>
                                    <td>
                                        @php
                                            $totalCompleted = array_sum(array_column($appointmentsAnalytics, 'completed'));
                                            $totalCancelled = array_sum(array_column($appointmentsAnalytics, 'cancelled'));
                                            $total = $totalCompleted + $totalCancelled;
                                            $rate = $total > 0 ? round(($totalCompleted / $total) * 100, 1) : 0;
                                        @endphp
                                        {{ $rate }}%
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Performance Analysis Section -->
        @if(!empty($performanceAnalysis))
            <div class="flux-card p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">Performance Analysis</h2>
                <div class="space-y-2">
                    @if($performanceAnalysis['most_requested_documents'])
                        <p><strong>Most Requested Documents:</strong> {{ $performanceAnalysis['most_requested_documents'] }}</p>
                    @endif
                    @if($performanceAnalysis['highest_completion_rate_purpose'])
                        <p><strong>Appointment Purpose with Highest Completion Rate:</strong> {{ $performanceAnalysis['highest_completion_rate_purpose'] }}</p>
                    @endif
                    @if(!empty($performanceAnalysis['increases']))
                        <p><strong>Increases Compared to Last Period:</strong></p>
                        <ul class="list-disc list-inside ml-4">
                            @foreach($performanceAnalysis['increases'] as $increase)
                                <li>{{ $increase }}</li>
                            @endforeach
                        </ul>
                    @endif
                    @if(!empty($performanceAnalysis['decreases']))
                        <p><strong>Decreases Compared to Last Period:</strong></p>
                        <ul class="list-disc list-inside ml-4">
                            @foreach($performanceAnalysis['decreases'] as $decrease)
                                <li>{{ $decrease }}</li>
                            @endforeach
                        </ul>
                    @endif
                    @if(empty($performanceAnalysis['increases']) && empty($performanceAnalysis['decreases']))
                        <p class="text-gray-500">No period-over-period comparison available (insufficient historical data).</p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Overall Summary Section -->
        @if(!empty($overallSummary))
            <div class="flux-card p-6">
                <h2 class="text-xl font-bold mb-4">
                    {{ $isMultiOffice ? 'Overall Summary (All Offices)' : 'Overall Summary' }}
                </h2>
                <div class="overflow-x-auto">
                    <table class="flux-table w-full">
                        <thead>
                            <tr>
                                <th>Office</th>
                                <th>Completed</th>
                                <th>Cancelled</th>
                                <th>Total Transactions</th>
                                <th>Overall Completion Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($overallSummary as $key => $item)
                                @if($key === 'grand_total')
                                    <tr class="font-bold bg-gray-100">
                                        <td>{{ $item['office'] }}</td>
                                        <td>{{ $item['completed'] }}</td>
                                        <td>{{ $item['cancelled'] }}</td>
                                        <td>{{ $item['total_transactions'] }}</td>
                                        <td>{{ $item['completion_rate'] }}%</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td>{{ $item['office'] }}</td>
                                        <td>{{ $item['completed'] }}</td>
                                        <td>{{ $item['cancelled'] }}</td>
                                        <td>{{ $item['total_transactions'] }}</td>
                                        <td>{{ $item['completion_rate'] }}%</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif
</div>
