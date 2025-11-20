<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Admin Service Report - All Offices</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }

        .report-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .report-period {
            font-size: 12px;
            margin-top: 5px;
        }

        .generated-date {
            font-size: 10px;
            margin-top: 10px;
            font-style: italic;
        }

        .office-section {
            margin-top: 30px;
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .office-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #333;
        }

        .section {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 9px;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .performance-analysis {
            margin-top: 15px;
            padding: 8px;
            background-color: #f9f9f9;
            border-left: 3px solid #333;
            font-size: 10px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9px;
            color: #666;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="report-title">Admin Service Report - All Offices</div>
        <div class="generated-date">Generated on: {{ $generated_date }}</div>
        <div class="report-period">{{ $report_period }}</div>
    </div>

    @php
        $officesAnalytics = $offices_analytics ?? [];
    @endphp

    @foreach($officesAnalytics as $officeData)
        @php
            $office = $officeData['office'];
            $documentRequests = $officeData['document_requests'] ?? [];
            $appointments = $officeData['appointments'] ?? [];
            $overallSummary = $officeData['overall_summary'] ?? [];
            $performanceAnalysis = $officeData['performance_analysis'] ?? [];
        @endphp

        <div class="office-section">
            <div class="office-title">{{ $office->name }}</div>

            @if(!empty($documentRequests))
                <div class="section">
                    <div class="section-title">Document Requests</div>
                    <table>
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
                            <tr class="total-row">
                                <td><strong>Total Document Requests</strong></td>
                                <td><strong>{{ array_sum(array_column($documentRequests, 'completed')) }}</strong></td>
                                <td><strong>{{ array_sum(array_column($documentRequests, 'cancelled')) }}</strong></td>
                                <td>
                                    <strong>
                                        @php
                                            $totalCompleted = array_sum(array_column($documentRequests, 'completed'));
                                            $totalCancelled = array_sum(array_column($documentRequests, 'cancelled'));
                                            $total = $totalCompleted + $totalCancelled;
                                            $rate = $total > 0 ? round(($totalCompleted / $total) * 100, 1) : 0;
                                        @endphp
                                        {{ $rate }}%
                                    </strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif

            @if(!empty($appointments))
                <div class="section">
                    <div class="section-title">Appointments</div>
                    <table>
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
                            <tr class="total-row">
                                <td><strong>Total Appointments</strong></td>
                                <td><strong>{{ array_sum(array_column($appointments, 'completed')) }}</strong></td>
                                <td><strong>{{ array_sum(array_column($appointments, 'cancelled')) }}</strong></td>
                                <td>
                                    <strong>
                                        @php
                                            $totalCompleted = array_sum(array_column($appointments, 'completed'));
                                            $totalCancelled = array_sum(array_column($appointments, 'cancelled'));
                                            $total = $totalCompleted + $totalCancelled;
                                            $rate = $total > 0 ? round(($totalCompleted / $total) * 100, 1) : 0;
                                        @endphp
                                        {{ $rate }}%
                                    </strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif

            @if(!empty($performanceAnalysis))
                <div class="section">
                    <div class="section-title">Performance Analysis</div>
                    <div class="performance-analysis">
                        @if(!empty($performanceAnalysis['most_scheduled_appointment']))
                            <p><strong>Most Scheduled Appointment:</strong> {{ $performanceAnalysis['most_scheduled_appointment'] }}</p>
                        @else
                            <p class="text-gray-500">No appointment data available for the selected period.</p>
                        @endif
                    </div>
                </div>
            @endif

            @if(!empty($overallSummary))
                <div class="section">
                    <div class="section-title">Overall Summary</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Completed</th>
                                <th>Cancelled</th>
                                <th>Total Transactions</th>
                                <th>Overall Completion Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="total-row">
                                <td><strong>{{ $overallSummary['completed'] }}</strong></td>
                                <td><strong>{{ $overallSummary['cancelled'] }}</strong></td>
                                <td><strong>{{ $overallSummary['total_transactions'] }}</strong></td>
                                <td><strong>{{ $overallSummary['completion_rate'] }}%</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endforeach

    @php
        $overallStats = $overall_statistics ?? [];
    @endphp

    @if(!empty($overallStats))
        <div class="office-section" style="page-break-before: always; border-top: 3px solid #333; padding-top: 20px;">
            <div class="office-title" style="font-size: 18px;">Overall Statistics (All Offices)</div>

            @if(!empty($overallStats['document_requests']['by_type']))
                <div class="section">
                    <div class="section-title">Document Requests - Overall Summary</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Document Type</th>
                                <th>Completed</th>
                                <th>Cancelled</th>
                                <th>Completion Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($overallStats['document_requests']['by_type'] as $item)
                                <tr>
                                    <td>{{ $item['document_type'] }}</td>
                                    <td>{{ $item['completed'] }}</td>
                                    <td>{{ $item['cancelled'] }}</td>
                                    <td>{{ $item['completion_rate'] }}%</td>
                                </tr>
                            @endforeach
                            <tr class="total-row" style="background-color: #e8e8e8;">
                                <td><strong>Total Document Requests</strong></td>
                                <td><strong>{{ $overallStats['document_requests']['total_completed'] }}</strong></td>
                                <td><strong>{{ $overallStats['document_requests']['total_cancelled'] }}</strong></td>
                                <td><strong>{{ $overallStats['document_requests']['completion_rate'] }}%</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif

            @if(!empty($overallStats['appointments']['by_purpose']))
                <div class="section">
                    <div class="section-title">Appointments - Overall Summary</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Purpose</th>
                                <th>Completed</th>
                                <th>Cancelled</th>
                                <th>Completion Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($overallStats['appointments']['by_purpose'] as $item)
                                <tr>
                                    <td>{{ $item['purpose'] }}</td>
                                    <td>{{ $item['completed'] }}</td>
                                    <td>{{ $item['cancelled'] }}</td>
                                    <td>{{ $item['completion_rate'] }}%</td>
                                </tr>
                            @endforeach
                            <tr class="total-row" style="background-color: #e8e8e8;">
                                <td><strong>Total Appointments</strong></td>
                                <td><strong>{{ $overallStats['appointments']['total_completed'] }}</strong></td>
                                <td><strong>{{ $overallStats['appointments']['total_cancelled'] }}</strong></td>
                                <td><strong>{{ $overallStats['appointments']['completion_rate'] }}%</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="section">
                <div class="section-title" style="font-size: 14px;">Grand Total Summary</div>
                <table style="font-size: 11px;">
                    <thead>
                        <tr>
                            <th style="font-size: 11px;">Total Offices</th>
                            <th style="font-size: 11px;">Total Completed</th>
                            <th style="font-size: 11px;">Total Cancelled</th>
                            <th style="font-size: 11px;">Total Transactions</th>
                            <th style="font-size: 11px;">Overall Completion Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="total-row" style="background-color: #d4d4d4; font-size: 12px;">
                            <td><strong>{{ $overallStats['total_offices'] }}</strong></td>
                            <td><strong>{{ $overallStats['grand_total']['completed'] }}</strong></td>
                            <td><strong>{{ $overallStats['grand_total']['cancelled'] }}</strong></td>
                            <td><strong>{{ $overallStats['grand_total']['total_transactions'] }}</strong></td>
                            <td><strong style="font-size: 13px;">{{ $overallStats['grand_total']['completion_rate'] }}%</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="footer">
        This is an automatically generated report. For any inquiries, please contact the office directly.
    </div>
</body>

</html>

