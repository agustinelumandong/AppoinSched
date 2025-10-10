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
            return redirect()->back()->with('error', 'No export parameters found.');
        }

        $data = $exportParams['data'];
        $officeInfo = $exportParams['officeInfo'];
        $reportTitle = $exportParams['reportTitle'];
        $reportPeriod = $exportParams['reportPeriod'];
        $filename = $exportParams['filename'];

        // Generate PDF
        $pdf = PDF::loadView('reports.export-pdf', [
            'data' => $data,
            'officeInfo' => $officeInfo,
            'reportTitle' => $reportTitle,
            'reportPeriod' => $reportPeriod,
            'generatedDate' => now()->format('F d, Y h:i A'),
        ]);

        // Clear export parameters from session
        session()->forget('pdf_export_params');

        // Return PDF for download
        return $pdf->download($filename);
    }

    public function generateCsv(Request $request)
    {
        // Get CSV export parameters from session
        $csvContent = session('csv_export_content');
        $filename = session('csv_export_filename');

        if (!$csvContent || !$filename) {
            return redirect()->back()->with('error', 'No CSV export data found.');
        }

        // Clear CSV export parameters from session
        session()->forget(['csv_export_content', 'csv_export_filename']);

        // Return CSV for download
        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
