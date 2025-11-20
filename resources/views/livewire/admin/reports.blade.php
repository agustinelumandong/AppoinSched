<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use App\Models\Appointments;
use App\Models\DocumentRequest;
use App\Models\Offices;
use App\Models\Services;
use Carbon\Carbon;

new class extends Component {
    public ?int $selectedServiceId = null;
    public string $periodType = 'monthly';
    public string $startDate;
    public string $endDate;
    public bool $isCustomRange = false;

    // Analytics data per office
    public array $officesAnalytics = [];
    public bool $isExporting = false;

    public function mount(): void
    {
        if (!auth()->user()->hasAnyRole(['admin', 'super-admin'])) {
            abort(403, 'Unauthorized access');
        }

        // Set default to current month
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->periodType = 'monthly';

        $this->generateAllOfficesAnalytics();
    }

    public function updatedPeriodType(): void
    {
        $this->isCustomRange = $this->periodType === 'custom';

        if (!$this->isCustomRange) {
            $this->updateDatesForPeriod();
        }

        $this->generateAllOfficesAnalytics();
    }

    public function updatedSelectedServiceId(): void
    {
        $this->generateAllOfficesAnalytics();
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
        $this->generateAllOfficesAnalytics();
    }

    public function generateAllOfficesAnalytics(): void
    {
        $offices = Offices::all();
        $startDate = Carbon::parse($this->startDate)->startOfDay();
        $endDate = Carbon::parse($this->endDate)->endOfDay();
        $serviceId = $this->selectedServiceId;

        $this->officesAnalytics = [];

        foreach ($offices as $office) {
            $officeId = $office->id;

            // Document Requests Analytics
            $docQuery = DocumentRequest::with(['service'])
                ->where('office_id', $officeId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('status', ['complete', 'cancelled']);

            if ($serviceId) {
                $docQuery->where('service_id', $serviceId);
            }

            $docRequests = $docQuery->get();
            $docByService = $docRequests->groupBy('service_id');
            $documentRequestsAnalytics = [];

            foreach ($docByService as $svcId => $requests) {
                $service = $requests->first()->service;
                $completed = $requests->where('status', 'complete')->count();
                $cancelled = $requests->where('status', 'cancelled')->count();
                $total = $completed + $cancelled;
                $completionRate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;

                $documentRequestsAnalytics[] = [
                    'document_type' => $service->title ?? 'N/A',
                    'completed' => $completed,
                    'cancelled' => $cancelled,
                    'completion_rate' => $completionRate,
                    'total' => $total,
                ];
            }

            // Appointments Analytics
            $appQuery = Appointments::with(['appointmentDetails'])
                ->where('office_id', $officeId)
                ->whereBetween('booking_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->whereIn('status', ['completed', 'cancelled']);

            $appointments = $appQuery->get();

            // Process purposes - handle "others" case
            $purposeMap = [];
            foreach ($appointments as $appointment) {
                $purpose = $appointment->purpose;

                // If purpose is "others", try to get actual purpose from notes or appointment details
                if (strtolower($purpose) === 'others' || strtolower($purpose) === 'other') {
                    // Check notes field for actual purpose
                    $notes = $appointment->notes ?? '';
                    // Check appointment details
                    $detailsPurpose = $appointment->appointmentDetails?->purpose ?? '';

                    // Try to extract actual purpose from notes (format: "Appointment for myself - {actual purpose}")
                    if (str_contains($notes, ' - ')) {
                        $parts = explode(' - ', $notes);
                        if (count($parts) > 1 && strtolower(trim($parts[1])) !== 'others') {
                            $purpose = trim($parts[1]);
                        } elseif (!empty($detailsPurpose) && strtolower($detailsPurpose) !== 'others') {
                            $purpose = $detailsPurpose;
                        }
                    } elseif (!empty($detailsPurpose) && strtolower($detailsPurpose) !== 'others') {
                        $purpose = $detailsPurpose;
                    }
                }

                // Use purpose as key for grouping
                if (!isset($purposeMap[$purpose])) {
                    $purposeMap[$purpose] = [];
                }
                $purposeMap[$purpose][] = $appointment;
            }

            $appointmentsAnalytics = [];
            foreach ($purposeMap as $purpose => $apps) {
                $completed = collect($apps)->where('status', 'completed')->count();
                $cancelled = collect($apps)->where('status', 'cancelled')->count();
                $total = $completed + $cancelled;
                $completionRate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;

                $appointmentsAnalytics[] = [
                    'purpose' => $this->formatPurpose($purpose),
                    'purpose_raw' => $purpose, // Keep raw for grouping
                    'completed' => $completed,
                    'cancelled' => $cancelled,
                    'completion_rate' => $completionRate,
                    'total' => $total,
                ];
            }

            // Overall Summary for this office
            $docCompleted = $docRequests->where('status', 'complete')->count();
            $docCancelled = $docRequests->where('status', 'cancelled')->count();
            $appCompleted = $appointments->where('status', 'completed')->count();
            $appCancelled = $appointments->where('status', 'cancelled')->count();

            $totalCompleted = $docCompleted + $appCompleted;
            $totalCancelled = $docCancelled + $appCancelled;
            $totalTransactions = $totalCompleted + $totalCancelled;
            $overallRate = $totalTransactions > 0 ? round(($totalCompleted / $totalTransactions) * 100, 1) : 0;

            // Performance Analysis
            $performanceAnalysis = $this->calculateOfficePerformanceAnalysis($officeId, $serviceId, $startDate, $endDate);

            $this->officesAnalytics[] = [
                'office' => $office,
                'office_id' => $officeId,
                'document_requests' => $documentRequestsAnalytics,
                'appointments' => $appointmentsAnalytics,
                'overall_summary' => [
                    'completed' => $totalCompleted,
                    'cancelled' => $totalCancelled,
                    'total_transactions' => $totalTransactions,
                    'completion_rate' => $overallRate,
                ],
                'performance_analysis' => $performanceAnalysis,
            ];
        }
    }

    protected function formatPurpose(string $purpose): string
    {
        if (empty($purpose)) {
            return 'N/A';
        }

        // If it's already a sentence or custom text (contains spaces and not kebab-case), return as is
        if (str_contains($purpose, ' ') && !str_contains($purpose, '-')) {
            return ucfirst($purpose);
        }

        // Convert kebab-case to Title Case
        return ucwords(str_replace('-', ' ', $purpose));
    }

    protected function calculateOfficePerformanceAnalysis(int $officeId, ?int $serviceId, Carbon $startDate, Carbon $endDate): array
    {
        $analysis = [
            'most_scheduled_appointment' => null,
            'most_requested_document' => null,
        ];

        // Get most requested document
        $docQuery = DocumentRequest::with(['service'])
            ->where('office_id', $officeId)
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
                $count = $mostRequested->count();
                $analysis['most_requested_document'] = ($mostRequested->first()->service->title ?? 'N/A') . ' (' . $count . ' request' . ($count > 1 ? 's' : '') . ')';
            }
        }

        // Get all appointments (including all statuses to find most scheduled)
        $appQuery = Appointments::with(['appointmentDetails'])
            ->where('office_id', $officeId)
            ->whereBetween('booking_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

        $appointments = $appQuery->get();

        if ($appointments->isNotEmpty()) {
            // Process purposes - handle "others" case
            $purposeMap = [];
            foreach ($appointments as $appointment) {
                $purpose = $appointment->purpose;

                // If purpose is "others", try to get actual purpose from notes or appointment details
                if (strtolower($purpose) === 'others' || strtolower($purpose) === 'other') {
                    $notes = $appointment->notes ?? '';
                    $detailsPurpose = $appointment->appointmentDetails?->purpose ?? '';

                    if (str_contains($notes, ' - ')) {
                        $parts = explode(' - ', $notes);
                        if (count($parts) > 1 && strtolower(trim($parts[1])) !== 'others') {
                            $purpose = trim($parts[1]);
                        } elseif (!empty($detailsPurpose) && strtolower($detailsPurpose) !== 'others') {
                            $purpose = $detailsPurpose;
                        }
                    } elseif (!empty($detailsPurpose) && strtolower($detailsPurpose) !== 'others') {
                        $purpose = $detailsPurpose;
                    }
                }

                if (!isset($purposeMap[$purpose])) {
                    $purposeMap[$purpose] = 0;
                }
                $purposeMap[$purpose]++;
            }

            // Find the most scheduled purpose
            if (!empty($purposeMap)) {
                arsort($purposeMap);
                $mostScheduledPurpose = array_key_first($purposeMap);
                $count = $purposeMap[$mostScheduledPurpose];

                if ($mostScheduledPurpose) {
                    $analysis['most_scheduled_appointment'] = $this->formatPurpose($mostScheduledPurpose) . ' (' . $count . ' appointment' . ($count > 1 ? 's' : '') . ')';
                }
            }
        }

        return $analysis;
    }

    public function exportToPdf(): void
    {
        try {
            $this->isExporting = true;

            // Ensure analytics are generated
            $this->generateAllOfficesAnalytics();

            // Calculate overall statistics across all offices
            $overallStats = $this->calculateOverallStatistics();

            // Prepare analytics data for PDF
            $pdfData = [
                'offices_analytics' => $this->officesAnalytics,
                'overall_statistics' => $overallStats,
                'report_period' => $this->getReportPeriodText(),
                'generated_date' => now()->format('F d, Y'),
                'period_type' => $this->periodType,
                'is_admin_report' => true,
            ];

            // Store in session for PDF generation
            session(['pdf_export_params' => $pdfData]);
            session()->save();

            $this->isExporting = false;

            // Dispatch event to trigger PDF download
            $this->dispatch('trigger-pdf-download', url: route('reports.export.pdf'));
        } catch (\Exception $e) {
            $this->isExporting = false;
            session()->flash('error', 'Failed to prepare PDF export: ' . $e->getMessage());
        }
    }

    protected function calculateOverallStatistics(): array
    {
        $totalDocCompleted = 0;
        $totalDocCancelled = 0;
        $totalAppCompleted = 0;
        $totalAppCancelled = 0;
        $allDocumentTypes = [];
        $allPurposes = [];

        foreach ($this->officesAnalytics as $officeData) {
            // Sum document requests
            foreach ($officeData['document_requests'] as $doc) {
                $docType = $doc['document_type'];
                if (!isset($allDocumentTypes[$docType])) {
                    $allDocumentTypes[$docType] = ['completed' => 0, 'cancelled' => 0];
                }
                $allDocumentTypes[$docType]['completed'] += $doc['completed'];
                $allDocumentTypes[$docType]['cancelled'] += $doc['cancelled'];
                $totalDocCompleted += $doc['completed'];
                $totalDocCancelled += $doc['cancelled'];
            }

            // Sum appointments
            foreach ($officeData['appointments'] as $app) {
                $purpose = $app['purpose'];
                if (!isset($allPurposes[$purpose])) {
                    $allPurposes[$purpose] = ['completed' => 0, 'cancelled' => 0];
                }
                $allPurposes[$purpose]['completed'] += $app['completed'];
                $allPurposes[$purpose]['cancelled'] += $app['cancelled'];
                $totalAppCompleted += $app['completed'];
                $totalAppCancelled += $app['cancelled'];
            }
        }

        // Calculate completion rates
        $totalDoc = $totalDocCompleted + $totalDocCancelled;
        $totalApp = $totalAppCompleted + $totalAppCancelled;
        $totalCompleted = $totalDocCompleted + $totalAppCompleted;
        $totalCancelled = $totalDocCancelled + $totalAppCancelled;
        $grandTotal = $totalCompleted + $totalCancelled;

        // Format document types with completion rates
        $documentTypesSummary = [];
        foreach ($allDocumentTypes as $type => $counts) {
            $total = $counts['completed'] + $counts['cancelled'];
            $rate = $total > 0 ? round(($counts['completed'] / $total) * 100, 1) : 0;
            $documentTypesSummary[] = [
                'document_type' => $type,
                'completed' => $counts['completed'],
                'cancelled' => $counts['cancelled'],
                'completion_rate' => $rate,
            ];
        }

        // Format purposes with completion rates
        $purposesSummary = [];
        foreach ($allPurposes as $purpose => $counts) {
            $total = $counts['completed'] + $counts['cancelled'];
            $rate = $total > 0 ? round(($counts['completed'] / $total) * 100, 1) : 0;
            $purposesSummary[] = [
                'purpose' => $this->formatPurpose($purpose),
                'purpose_raw' => $purpose,
                'completed' => $counts['completed'],
                'cancelled' => $counts['cancelled'],
                'completion_rate' => $rate,
            ];
        }

        return [
            'document_requests' => [
                'by_type' => $documentTypesSummary,
                'total_completed' => $totalDocCompleted,
                'total_cancelled' => $totalDocCancelled,
                'total' => $totalDoc,
                'completion_rate' => $totalDoc > 0 ? round(($totalDocCompleted / $totalDoc) * 100, 1) : 0,
            ],
            'appointments' => [
                'by_purpose' => $purposesSummary,
                'total_completed' => $totalAppCompleted,
                'total_cancelled' => $totalAppCancelled,
                'total' => $totalApp,
                'completion_rate' => $totalApp > 0 ? round(($totalAppCompleted / $totalApp) * 100, 1) : 0,
            ],
            'grand_total' => [
                'completed' => $totalCompleted,
                'cancelled' => $totalCancelled,
                'total_transactions' => $grandTotal,
                'completion_rate' => $grandTotal > 0 ? round(($totalCompleted / $grandTotal) * 100, 1) : 0,
            ],
            'total_offices' => count($this->officesAnalytics),
        ];
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
        return [
            'services' => Services::all(),
        ];
    }
}; ?>

<div x-data x-on:trigger-pdf-download.window="setTimeout(() => window.open($event.detail.url, '_blank'), 150)">
    @include('components.alert')


    <div class="flux-card p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Admin Reports</h1>
                <p class="text-gray-600 mt-1">Analytics and performance metrics for all offices</p>
            </div>
        </div>
    </div>

    <div class="flux-card p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
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

            <div class="flex items-end">
                <button type="button" wire:click="exportToPdf" class="flux-btn flux-btn-danger text-sm w-full"
                    style="font-size: 12px;">
                    <span wire:loading.remove wire:target="exportToPdf">
                        <i class="bi bi-file-earmark-pdf mr-1"></i> Export All to PDF
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

    @if(empty($officesAnalytics))
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
        @foreach($officesAnalytics as $officeData)
            @php
                $office = $officeData['office'];
                $documentRequests = $officeData['document_requests'];
                $appointments = $officeData['appointments'];
                $overallSummary = $officeData['overall_summary'];
                $performanceAnalysis = $officeData['performance_analysis'];
            @endphp

            <div class="flux-card p-6 mb-6">
                <div class="border-b-2 border-gray-300 pb-4 mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $office->name }}</h2>
                </div>

                <!-- Document Requests Section -->
                @if(!empty($documentRequests))
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-3">Document Requests</h3>
                        <div class="overflow-x-auto">
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
                                    @foreach($documentRequests as $item)
                                        <tr>
                                            <td>{{ $item['document_type'] }}</td>
                                            <td>{{ $item['completed'] }}</td>
                                            <td>{{ $item['cancelled'] }}</td>
                                            <td>{{ $item['completion_rate'] }}%</td>
                                        </tr>
                                    @endforeach
                                    <tr class="font-bold bg-gray-50">
                                        <td>Total Document Requests</td>
                                        <td>{{ array_sum(array_column($documentRequests, 'completed')) }}</td>
                                        <td>{{ array_sum(array_column($documentRequests, 'cancelled')) }}</td>
                                        <td>
                                            @php
                                                $totalCompleted = array_sum(array_column($documentRequests, 'completed'));
                                                $totalCancelled = array_sum(array_column($documentRequests, 'cancelled'));
                                                $total = $totalCompleted + $totalCancelled;
                                                $rate = $total > 0 ? round(($totalCompleted / $total) * 100, 1) : 0;
                                            @endphp
                                            {{ $rate }}%
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Appointments Section -->
                @if(!empty($appointments))
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-3">Appointments</h3>
                        <div class="overflow-x-auto">
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
                                    @foreach($appointments as $item)
                                        <tr>
                                            <td>{{ $item['purpose'] }}</td>
                                            <td>{{ $item['completed'] }}</td>
                                            <td>{{ $item['cancelled'] }}</td>
                                            <td>{{ $item['completion_rate'] }}%</td>
                                        </tr>
                                    @endforeach
                                    <tr class="font-bold bg-gray-50">
                                        <td>Total Appointments</td>
                                        <td>{{ array_sum(array_column($appointments, 'completed')) }}</td>
                                        <td>{{ array_sum(array_column($appointments, 'cancelled')) }}</td>
                                        <td>
                                            @php
                                                $totalCompleted = array_sum(array_column($appointments, 'completed'));
                                                $totalCancelled = array_sum(array_column($appointments, 'cancelled'));
                                                $total = $totalCompleted + $totalCancelled;
                                                $rate = $total > 0 ? round(($totalCompleted / $total) * 100, 1) : 0;
                                            @endphp
                                            {{ $rate }}%
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Performance Analysis Section -->
                @if(!empty($performanceAnalysis))
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-3">Performance Analysis</h3>
                        <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                            @if($performanceAnalysis['most_scheduled_appointment'])
                                <p><strong>Most Scheduled Appointment:</strong> {{ $performanceAnalysis['most_scheduled_appointment'] }}</p>
                            @endif
                            @if($performanceAnalysis['most_requested_document'])
                                <p><strong>Most Requested Document:</strong> {{ $performanceAnalysis['most_requested_document'] }}</p>
                            @endif
                            @if(empty($performanceAnalysis['most_scheduled_appointment']) && empty($performanceAnalysis['most_requested_document']))
                                <p class="text-gray-500">No data available for the selected period.</p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Overall Summary Section -->
                @if(!empty($overallSummary))
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Overall Summary</h3>
                        <div class="overflow-x-auto">
                            <table class="flux-table w-full">
                                <thead>
                                    <tr>
                                        <th>Completed</th>
                                        <th>Cancelled</th>
                                        <th>Total Transactions</th>
                                        <th>Overall Completion Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="font-bold bg-gray-50">
                                        <td>{{ $overallSummary['completed'] }}</td>
                                        <td>{{ $overallSummary['cancelled'] }}</td>
                                        <td>{{ $overallSummary['total_transactions'] }}</td>
                                        <td>{{ $overallSummary['completion_rate'] }}%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    @endif
</div>

