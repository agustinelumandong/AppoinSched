<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Monthly Service Report</title>
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

        .office-name {
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
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

        .section {
            margin-top: 25px;
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 14px;
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
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .grand-total-row {
            font-weight: bold;
            background-color: #e8e8e8;
        }

        .performance-analysis {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 3px solid #333;
        }

        .performance-analysis ul {
            margin: 5px 0;
            padding-left: 20px;
        }

        .performance-analysis li {
            margin: 3px 0;
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
        <div class="report-title">Monthly Service Report</div>
        <div class="generated-date">Generated on: {{ $generated_date }}</div>
        <div class="report-period">{{ $report_period }}</div>
    </div>

    @php
        $officeInfo = $office_info ?? [];
        $documentRequests = $document_requests ?? [];
        $appointments = $appointments ?? [];
        $performanceAnalysis = $performance_analysis ?? [];
        $overallSummary = $overall_summary ?? [];
        $selectedType = $selected_type ?? 'both';
        $isMultiOffice = is_array($overallSummary) && array_key_exists('grand_total', $overallSummary);
    @endphp

    @if(!empty($officeInfo['name']) && $officeInfo['name'] !== 'All Offices')
        <div class="office-name">{{ $officeInfo['name'] }}</div>
    @endif

    @if(($selectedType === 'documents' || $selectedType === 'both') && !empty($documentRequests))
        <div class="section">
            <div class="section-title">
                @if($isMultiOffice)
                    All Offices - Document Requests
                @else
                    {{ $officeInfo['name'] ?? 'Office' }} - Document Requests
                @endif
            </div>
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

    @if(($selectedType === 'appointments' || $selectedType === 'both') && !empty($appointments))
        <div class="section">
            <div class="section-title">
                @if($isMultiOffice)
                    All Offices - Appointments
                @else
                    {{ $officeInfo['name'] ?? 'Office' }} - Appointments
                @endif
            </div>
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
                @php
                    $selectedType = $selected_type ?? 'both';
                @endphp
                @if($selectedType === 'appointments' || $selectedType === 'both')
                    @if(!empty($performanceAnalysis['most_scheduled_appointment']))
                        <p><strong>Most Scheduled Appointment:</strong> {{ $performanceAnalysis['most_scheduled_appointment'] }}</p>
                    @else
                        <p>No appointment data available for the selected period.</p>
                    @endif
                @endif
                @if($selectedType === 'documents' || $selectedType === 'both')
                    @if(!empty($performanceAnalysis['most_requested_document']))
                        <p><strong>Most Requested Document:</strong> {{ $performanceAnalysis['most_requested_document'] }}</p>
                    @else
                        <p>No document request data available for the selected period.</p>
                    @endif
                @endif
            </div>
        </div>
    @endif

    @if(!empty($overallSummary))
        <div class="section">
            <div class="section-title">
                {{ $isMultiOffice ? 'Overall Summary (All Offices)' : 'Overall Summary' }}
            </div>
            <table>
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
                            <tr class="grand-total-row">
                                <td><strong>{{ $item['office'] }}</strong></td>
                                <td><strong>{{ $item['completed'] }}</strong></td>
                                <td><strong>{{ $item['cancelled'] }}</strong></td>
                                <td><strong>{{ $item['total_transactions'] }}</strong></td>
                                <td><strong>{{ $item['completion_rate'] }}%</strong></td>
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
    @endif

    <div class="footer">
        This is an automatically generated report. For any inquiries, please contact the office directly.
    </div>
</body>

</html>

