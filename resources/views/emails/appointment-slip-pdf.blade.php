<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Appointment Slip</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo {
            max-width: 100px;
            height: auto;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin: 10px 0;
        }

        .reference {
            font-size: 14px;
            color: #666;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .grid {
            display: table;
            width: 100%;
        }

        .row {
            display: table-row;
        }

        .col {
            display: table-cell;
            padding: 5px 10px;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            color: #555;
        }

        .value {
            color: #333;
        }

        .qr-section {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .notes {
            background-color: #f0f9ff;
            border: 1px solid #0ea5e9;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .notes h4 {
            margin-top: 0;
            color: #0c4a6e;
        }

        .notes ul {
            margin: 10px 0;
            padding-left: 20px;
        }

        .notes li {
            margin-bottom: 5px;
        }

        .status {
            color: #d97706;
            font-weight: bold;
        }

        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">Appointment Confirmation</div>
        {{-- <div class="reference">Reference: {{ $reference_number }}</div> --}}
    </div>

    <div class="section">
        <div class="section-title">Appointment Details</div>
        <div class="grid">
            <div class="row">
                <div class="col label">Office:</div>
                <div class="col value">{{ $office->name }}</div>
            </div>
            <div class="row">
                <div class="col label">Purpose:</div>
                <div class="col value">{{ $purpose }}</div>
            </div>
            <div class="row">
                <div class="col label">Date:</div>
                <div class="col value">{{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}</div>
            </div>
            <div class="row">
                <div class="col label">Time:</div>
                <div class="col value">{{ \Carbon\Carbon::parse($selectedTime)->format('h:i A') }}</div>
            </div>
        </div>

        <div class="grid">
            <div class="row">
                <div class="col label">Name:</div>
                <div class="col value">{{ $last_name }}, {{ $first_name }} {{ $middle_name ? $middle_name : '' }}
                </div>
            </div>
            <div class="row">
                <div class="col label">Email:</div>
                <div class="col value">{{ $email }}</div>
            </div>
            <div class="row">
                <div class="col label">Phone:</div>
                <div class="col value">{{ $phone }}</div>
            </div>
        </div>
    </div>


    @if (isset($metadata) && is_array($metadata) && count($metadata) > 0)
        <div class="section">
            <div class="section-title">Certificate Requests</div>
            <div class="grid">
                @foreach ($metadata as $index => $certificateData)
                    @if (isset($certificateData['certificate_code']))
                        <div class="row">
                            <div class="col label">Certificate #{{ $certificateData['number'] }}:</div>
                            <div class="col value" style="font-family: monospace; font-weight: bold;">
                                {{ $certificateData['certificate_code'] }}
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif


    <div class="qr-section">
        <div style="text-align: center; margin-bottom: 10px;">
            <strong>Reference Code: {{ $reference_number }}</strong>
        </div>
        <div style="text-align: center; font-size: 12px; color: #666;">
            Present this reference code at the office for verification
        </div>
    </div>

    <div class="notes">
        <h4>Important Notes:</h4>
        <ul>
            <li>Please arrive 15 minutes before your scheduled appointment.</li>
            <li>Bring a valid ID for verification.</li>
            <li>For cancellations, please contact us at least 24 hours in advance.</li>
        </ul>
    </div>
</body>

</html>