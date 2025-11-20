<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function generatePdf(Request $request)
    {
        // Get export parameters from session
        $exportParams = session('pdf_export_params');

        if (!$exportParams) {
            $redirectRoute = auth()->user()->hasAnyRole(['admin', 'super-admin'])
                ? 'admin.reports'
                : 'staff.reports';
            return redirect()->route($redirectRoute)->with('error', 'No export parameters found. Please generate the report again.');
        }

        try {
            // Determine which template to use
            $isAdminReport = $exportParams['is_admin_report'] ?? false;
            $viewName = $isAdminReport ? 'reports.export-pdf-admin' : 'reports.export-pdf-analytics';

            // Generate PDF with analytics format
            $pdf = PDF::loadView($viewName, $exportParams);

            // Clear export parameters from session
            session()->forget('pdf_export_params');

            // Generate filename
            $periodType = $exportParams['period_type'] ?? 'monthly';
            $filename = ($isAdminReport ? 'admin_' : '') . 'service_report_' . $periodType . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';

            // Return PDF for download
            return $pdf->download($filename);
        } catch (\Exception $e) {
            // Log error and redirect back
            \Log::error('PDF generation failed: ' . $e->getMessage());
            $redirectRoute = auth()->user()->hasAnyRole(['admin', 'super-admin'])
                ? 'admin.reports'
                : 'staff.reports';
            return redirect()->route($redirectRoute)->with('error', 'Failed to generate PDF. Please try again.');
        }
    }

}
