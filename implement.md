---
tools: ['codebase', 'textSearch']
---

@workspace /explain #codebase Based on the project Im planning to make a  Generate reports: 
1- All appointments in a daily/weekly/monthly  for each different offices and services
2- All Document Request  in a daily/weekly/monthly for each different offices and services
3- All appointments by status  for each different offices and services
4- All Document Request by status  for each different offices and services

what can you suggest to me ?

> Copilot: # Report Generation System for AppoinSched
> 
> Based on your requirements, I recommend implementing a comprehensive reporting system using Laravel's built-in features, Livewire Volt components, and a charting library. Here's a structured approach:
> 
> ## 1. Create a Reports Module
> 
> ### Reports Controller and Service Layer
> 
> ```php
> <?php
> 
> declare(strict_types=1);
> 
> namespace App\Services;
> 
> use App\Models\Appointments;
> use App\Models\DocumentRequest;
> use App\Models\Offices;
> use App\Models\Services;
> use Carbon\Carbon;
> use Illuminate\Database\Eloquent\Builder;
> use Illuminate\Support\Collection;
> 
> class ReportService
> {
>     /**
>      * Get appointment reports based on filters
>      */
>     public function getAppointmentsReport(
>         ?int $officeId = null,
>         ?int $serviceId = null,
>         ?string $status = null,
>         ?string $period = null,
>         ?string $startDate = null,
>         ?string $endDate = null
>     ): Collection {
>         $query = Appointments::with(['office', 'service', 'user'])
>             ->when($officeId, fn(Builder $query) => $query->where('office_id', $officeId))
>             ->when($serviceId, fn(Builder $query) => $query->where('service_id', $serviceId))
>             ->when($status, fn(Builder $query) => $query->where('status', $status));
> 
>         return $this->applyDateFilters($query, $period, $startDate, $endDate)
>             ->get()
>             ->groupBy(function($item) use ($period) {
>                 if ($period === 'daily') {
>                     return $item->booking_date->format('Y-m-d');
>                 } elseif ($period === 'weekly') {
>                     return $item->booking_date->format('Y-W');
>                 } elseif ($period === 'monthly') {
>                     return $item->booking_date->format('Y-m');
>                 }
>                 return 'all';
>             });
>     }
> 
>     /**
>      * Get document request reports based on filters
>      */
>     public function getDocumentRequestsReport(
>         ?int $officeId = null,
>         ?int $serviceId = null,
>         ?string $status = null,
>         ?string $period = null,
>         ?string $startDate = null,
>         ?string $endDate = null
>     ): Collection {
>         $query = DocumentRequest::with(['office', 'service', 'user'])
>             ->when($officeId, fn(Builder $query) => $query->where('office_id', $officeId))
>             ->when($serviceId, fn(Builder $query) => $query->where('service_id', $serviceId))
>             ->when($status, fn(Builder $query) => $query->where('status', $status));
> 
>         return $this->applyDateFilters($query, $period, $startDate, $endDate)
>             ->get()
>             ->groupBy(function($item) use ($period) {
>                 if ($period === 'daily') {
>                     return Carbon::parse($item->created_at)->format('Y-m-d');
>                 } elseif ($period === 'weekly') {
>                     return Carbon::parse($item->created_at)->format('Y-W');
>                 } elseif ($period === 'monthly') {
>                     return Carbon::parse($item->created_at)->format('Y-m');
>                 }
>                 return 'all';
>             });
>     }
> 
>     /**
>      * Apply date filters to query
>      */
>     private function applyDateFilters(Builder $query, ?string $period, ?string $startDate, ?string $endDate): Builder
>     {
>         if ($startDate && $endDate) {
>             return $query->whereBetween('created_at', [$startDate, $endDate]);
>         }
> 
>         if ($period === 'daily') {
>             return $query->whereDate('created_at', Carbon::today());
>         }
> 
>         if ($period === 'weekly') {
>             return $query->whereBetween('created_at', [
>                 Carbon::now()->startOfWeek(), 
>                 Carbon::now()->endOfWeek()
>             ]);
>         }
> 
>         if ($period === 'monthly') {
>             return $query->whereBetween('created_at', [
>                 Carbon::now()->startOfMonth(),
>                 Carbon::now()->endOfMonth()
>             ]);
>         }
> 
>         return $query;
>     }
> }
> ```
> 
> ### Volt Livewire Components
> 
> Create these Volt components for the reporting interface:
> 
> ```php
> <?php
> 
> declare(strict_types=1);
> 
> use Livewire\Volt\Component;
> use App\Models\Offices;
> use App\Models\Services;
> use App\Services\ReportService;
> use Illuminate\Support\Collection;
> use Livewire\WithPagination;
> use Carbon\Carbon;
> 
> new class extends Component {
>     use WithPagination;
>     
>     public ?int $selectedOfficeId = null;
>     public ?int $selectedServiceId = null;
>     public ?string $selectedStatus = null;
>     public string $periodType = 'daily';
>     public string $startDate;
>     public string $endDate;
>     public bool $isCustomRange = false;
>     public Collection $reportData;
>     public array $statusOptions = [
>         'pending', 'approved', 'completed', 'cancelled'
>     ];
>     
>     public function mount(): void
>     {
>         // Only staff and admins can access reports
>         if (!auth()->user()->hasAnyRole(['MCR-staff', 'MTO-staff', 'BPLS-staff', 'admin', 'super-admin'])) {
>             abort(403, 'Unauthorized access');
>         }
>         
>         // Initialize dates
>         $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
>         $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
>         
>         // If user is staff, pre-select their office
>         if (!auth()->user()->hasAnyRole(['admin', 'super-admin'])) {
>             $this->selectedOfficeId = auth()->user()->getOfficeIdForStaff();
>         }
>         
>         $this->generateReport();
>     }
>     
>     public function updatedPeriodType(): void
>     {
>         if ($this->periodType !== 'custom') {
>             $this->isCustomRange = false;
>         } else {
>             $this->isCustomRange = true;
>         }
>         
>         $this->generateReport();
>     }
>     
>     public function updatedSelectedOfficeId(): void
>     {
>         $this->selectedServiceId = null;
>         $this->generateReport();
>     }
>     
>     public function updatedSelectedServiceId(): void
>     {
>         $this->generateReport();
>     }
>     
>     public function updatedSelectedStatus(): void
>     {
>         $this->generateReport();
>     }
>     
>     public function applyDateFilter(): void
>     {
>         $this->validate([
>             'startDate' => 'required|date',
>             'endDate' => 'required|date|after_or_equal:startDate',
>         ]);
>         
>         $this->generateReport();
>     }
>     
>     public function generateReport(): void
>     {
>         $reportService = app(ReportService::class);
>         $this->reportData = $reportService->getAppointmentsReport(
>             $this->selectedOfficeId,
>             $this->selectedServiceId,
>             $this->selectedStatus,
>             $this->periodType !== 'custom' ? $this->periodType : null,
>             $this->isCustomRange ? $this->startDate : null,
>             $this->isCustomRange ? $this->endDate : null
>         );
>     }
>     
>     public function exportToCsv(): void
>     {
>         // Implement CSV export
>     }
>     
>     public function exportToPdf(): void
>     {
>         // Implement PDF export using Laravel PDF packages like Dompdf
>     }
>     
>     public function with(): array
>     {
>         return [
>             'offices' => Offices::all(),
>             'services' => $this->selectedOfficeId 
>                 ? Services::where('office_id', $this->selectedOfficeId)->get() 
>                 : collect(),
>         ];
>     }
> }; ?>
> 
> <div>
>     <!-- Header -->
>     <div class="flux-card p-6 mb-6">
>         <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
>             <div>
>                 <h1 class="text-2xl font-bold text-gray-900">Appointment Reports</h1>
>                 <p class="text-gray-600 mt-1">Generate and export appointment reports</p>
>             </div>
>         </div>
>     </div>
> 
>     <!-- Filters -->
>     <div class="flux-card p-6 mb-6">
>         <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
>             <!-- Office Filter -->
>             <div>
>                 <label for="officeFilter" class="block text-sm font-medium text-gray-700 mb-1">Office</label>
>                 <select id="officeFilter" wire:model.live="selectedOfficeId" class="form-select">
>                     <option value="">All Offices</option>
>                     @foreach($offices as $office)
>                         <option value="{{ $office->id }}">{{ $office->name }}</option>
>                     @endforeach
>                 </select>
>             </div>
> 
>             <!-- Service Filter -->
>             <div>
>                 <label for="serviceFilter" class="block text-sm font-medium text-gray-700 mb-1">Service</label>
>                 <select id="serviceFilter" wire:model.live="selectedServiceId" class="form-select">
>                     <option value="">All Services</option>
>                     @foreach($services as $service)
>                         <option value="{{ $service->id }}">{{ $service->title }}</option>
>                     @endforeach
>                 </select>
>             </div>
> 
>             <!-- Status Filter -->
>             <div>
>                 <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
>                 <select id="statusFilter" wire:model.live="selectedStatus" class="form-select">
>                     <option value="">All Statuses</option>
>                     @foreach($statusOptions as $status)
>                         <option value="{{ $status }}">{{ ucfirst($status) }}</option>
>                     @endforeach
>                 </select>
>             </div>
> 
>             <!-- Period Filter -->
>             <div>
>                 <label for="periodFilter" class="block text-sm font-medium text-gray-700 mb-1">Period</label>
>                 <select id="periodFilter" wire:model.live="periodType" class="form-select">
>                     <option value="daily">Daily</option>
>                     <option value="weekly">Weekly</option>
>                     <option value="monthly">Monthly</option>
>                     <option value="custom">Custom Range</option>
>                 </select>
>             </div>
>         </div>
> 
>         <!-- Custom Date Range -->
>         @if($isCustomRange)
>         <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
>             <div>
>                 <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
>                 <input type="date" id="startDate" wire:model="startDate" class="form-input">
>             </div>
>             <div>
>                 <label for="endDate" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
>                 <input type="date" id="endDate" wire:model="endDate" class="form-input">
>             </div>
>             <div>
>                 <button type="button" wire:click="applyDateFilter" class="flux-btn flux-btn-primary">
>                     Apply
>                 </button>
>             </div>
>         </div>
>         @endif
> 
>         <!-- Export Options -->
>         <div class="mt-4 flex gap-2">
>             <button type="button" wire:click="exportToCsv" class="flux-btn flux-btn-outline">
>                 <i class="bi bi-file-earmark-spreadsheet mr-1"></i> Export to CSV
>             </button>
>             <button type="button" wire:click="exportToPdf" class="flux-btn flux-btn-outline">
>                 <i class="bi bi-file-earmark-pdf mr-1"></i> Export to PDF
>             </button>
>         </div>
>     </div>
> 
>     <!-- Report Data -->
>     <div class="flux-card p-6">
>         <!-- Chart Section -->
>         <div class="mb-6">
>             <!-- We'll add chart rendering here using JavaScript -->
>             <div id="appointmentChart" style="height: 300px;"></div>
>         </div>
> 
>         <!-- Data Table -->
>         <div class="overflow-x-auto">
>             <table class="flux-table w-full">
>                 <thead>
>                     <tr>
>                         <th>Period</th>
>                         <th>Office</th>
>                         <th>Service</th>
>                         <th>Status</th>
>                         <th>Count</th>
>                     </tr>
>                 </thead>
>                 <tbody>
>                     @forelse($reportData as $period => $appointments)
>                         @php
>                             $groupedByOffice = $appointments->groupBy('office.name');
>                         @endphp
>                         
>                         @foreach($groupedByOffice as $officeName => $officeAppointments)
>                             @php
>                                 $groupedByService = $officeAppointments->groupBy('service.title');
>                             @endphp
>                             
>                             @foreach($groupedByService as $serviceName => $serviceAppointments)
>                                 @php
>                                     $groupedByStatus = $serviceAppointments->groupBy('status');
>                                 @endphp
>                                 
>                                 @foreach($groupedByStatus as $status => $statusAppointments)
>                                     <tr>
>                                         <td>{{ $period }}</td>
>                                         <td>{{ $officeName ?? 'N/A' }}</td>
>                                         <td>{{ $serviceName ?? 'N/A' }}</td>
>                                         <td>
>                                             <span class="flux-badge {{ match($status) {
>                                                 'pending' => 'flux-badge-warning',
>                                                 'approved' => 'flux-badge-success',
>                                                 'completed' => 'flux-badge-info',
>                                                 'cancelled' => 'flux-badge-danger',
>                                                 default => 'flux-badge-light'
>                                             } }}">
>                                                 {{ ucfirst($status) }}
>                                             </span>
>                                         </td>
>                                         <td>{{ $statusAppointments->count() }}</td>
>                                     </tr>
>                                 @endforeach
>                             @endforeach
>                         @endforeach
>                     @empty
>                         <tr>
>                             <td colspan="5" class="text-center py-8">
>                                 <div class="text-gray-500">
>                                     <i class="bi bi-bar-chart text-4xl mb-2"></i>
>                                     <p>No appointment data available for the selected filters.</p>
>                                 </div>
>                             </td>
>                         </tr>
>                     @endforelse
>                 </tbody>
>             </table>
>         </div>
>     </div>
> </div>
> ```
> 
> Create a similar component for document request reports.
> 
> ### 2. Implement Chart Visualization
> 
> Add a JavaScript chart library like Chart.js:
> 
> ```php
> <!-- Add this to the component's Blade section -->
> <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
> 
> <script>
>     document.addEventListener('livewire:initialized', () => {
>         let chart = null;
>         
>         function initializeChart(data) {
>             const ctx = document.getElementById('appointmentChart').getContext('2d');
>             
>             // Destroy previous chart instance if it exists
>             if (chart) {
>                 chart.destroy();
>             }
>             
>             // Process data for chart
>             const labels = [];
>             const datasets = [];
>             
>             // Create datasets by status
>             const statusColors = {
>                 'pending': 'rgba(255, 193, 7, 0.5)',
>                 'approved': 'rgba(40, 167, 69, 0.5)',
>                 'completed': 'rgba(23, 162, 184, 0.5)',
>                 'cancelled': 'rgba(220, 53, 69, 0.5)'
>             };
>             
>             // Create labels and datasets from the data
>             // This is simplified; you'll need to adapt this to your actual data structure
>             
>             chart = new Chart(ctx, {
>                 type: 'bar',
>                 data: {
>                     labels: labels,
>                     datasets: datasets
>                 },
>                 options: {
>                     responsive: true,
>                     plugins: {
>                         legend: {
>                             position: 'top',
>                         },
>                         title: {
>                             display: true,
>                             text: 'Appointment Report'
>                         }
>                     }
>                 }
>             });
>         }
>         
>         // Listen for report data updates
>         Livewire.on('reportGenerated', (data) => {
>             initializeChart(data);
>         });
>     });
> </script>
> ```
> 
> ### 3. Add Routes and Navigation
> 
> ```php
> // Add report routes
> Volt::route('admin/reports/appointments', 'reports.appointment-reports')
>     ->name('admin.reports.appointments')
>     ->middleware(['auth', 'role:admin|super-admin|MCR-staff|MTO-staff|BPLS-staff']);
> 
> Volt::route('admin/reports/documents', 'reports.document-reports')
>     ->name('admin.reports.documents')
>     ->middleware(['auth', 'role:admin|super-admin|MCR-staff|MTO-staff|BPLS-staff']);
> ```
> 
> ### 4. Create Report Export Functionality
> 
> You can use Laravel Excel or Dompdf for exports:
> 
> ```bash
> composer require maatwebsite/excel
> composer require barryvdh/laravel-dompdf
> ```
> 
> Add export methods to your ReportService:
> 
> ```php
> // Add these methods to the ReportService class
> 
> use Maatwebsite\Excel\Facades\Excel;
> use Barryvdh\DomPDF\Facade\PDF;
> use App\Exports\AppointmentExport;
> use App\Exports\DocumentRequestExport;
> 
> /**
>  * Export appointments report to CSV
>  */
> public function exportAppointmentsToCsv(Collection $data): \Symfony\Component\HttpFoundation\BinaryFileResponse
> {
>     return Excel::download(new AppointmentExport($data), 'appointments-report.csv');
> }
> 
> /**
>  * Export appointments report to PDF
>  */
> public function exportAppointmentsToPdf(Collection $data): \Illuminate\Http\Response
> {
>     $pdf = PDF::loadView('exports.appointments-pdf', ['reportData' => $data]);
>     return $pdf->download('appointments-report.pdf');
> }
> 
> // Similar methods for document requests
> ```
> 
> ## Integration Steps
> 
> 1. **Create the Necessary Files**:
>    - Create the ReportService class
>    - Create Volt Livewire components for report views
>    - Create export classes and PDF templates
> 
> 2. **Add Navigation Links**:
>    - Add links in the admin/staff sidebar navigation to the report pages
> 
> 3. **Role-Based Access**:
>    - Ensure only authorized users can access reports
>    - Staff should only see reports for their assigned office
> 
> ## Additional Features to Consider
> 
> 1. **Dashboard Summary Widgets**:
>    - Add report summary widgets to staff/admin dashboards
> 
> 2. **Scheduled Reports**:
>    - Allow users to schedule regular report generation and email delivery
> 
> 3. **Interactive Filters**:
>    - Add more advanced filters like date ranges, staff members, etc.
> 
> 4. **Comparative Analysis**:
>    - Compare data between different periods (e.g., this month vs. last month)
> 
> 5. **Data Visualizations**:
>    - Add more chart types (pie, line, area) for different insights
> 
> This implementation gives you a solid foundation for a comprehensive reporting system that can be expanded as needed.
