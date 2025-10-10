<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $reportTitle }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }

        .office-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .office-address {
            font-size: 12px;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 15px;
        }

        .report-period {
            font-size: 12px;
            margin-top: 5px;
        }

        .generated-date {
            font-size: 10px;
            margin-top: 5px;
            font-style: italic;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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

        .summary {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }

        .summary-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="office-name">{{ $officeInfo['name'] }}</div>
        @if (!empty($officeInfo['address']))
            <div class="office-address">{{ $officeInfo['address'] }}</div>
        @endif
        @if (!empty($officeInfo['contact']))
            <div class="office-address">{{ $officeInfo['contact'] }}</div>
        @endif
        <div class="report-title">{{ $reportTitle }}</div>
        <div class="report-period">{{ $reportPeriod }}</div>
        <div class="generated-date">Generated on: {{ $generatedDate }}</div>
    </div>

    @if (count($data) > 0)
        <table>
            <thead>
                <tr>
                    @foreach (array_keys($data[0]) as $header)
                        <th>{{ ucfirst(str_replace('_', ' ', $header)) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        @foreach ($row as $value)
                            <td>{{ $value }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-title">Summary</div>
            <p>Total Records: {{ count($data) }}</p>

            @php
                $statusCounts = [];
                foreach ($data as $row) {
                    $status = $row['status'] ?? 'Unknown';
                    if (!isset($statusCounts[$status])) {
                        $statusCounts[$status] = 0;
                    }
                    $statusCounts[$status]++;
                }
            @endphp

            <p>Status Breakdown:</p>
            <ul>
                @foreach ($statusCounts as $status => $count)
                    <li>{{ $status }}: {{ $count }}</li>
                @endforeach
            </ul>
        </div>
    @else
        <p>No data available for the selected filters.</p>
    @endif

    <div class="footer">
        This is an automatically generated report. For any inquiries, please contact the office directly.
    </div>
</body>

</html>
