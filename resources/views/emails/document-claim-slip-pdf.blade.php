<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Document Request Claim Slip</title>
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

        .title {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin: 10px 0;
        }

        .reference {
            font-size: 16px;
            color: #666;
            font-weight: bold;
            margin-top: 10px;
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
            width: 40%;
        }

        .value {
            color: #333;
        }

        .requirements {
            background-color: #f0f9ff;
            border: 1px solid #0ea5e9;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .requirements h4 {
            margin-top: 0;
            color: #0c4a6e;
            font-size: 16px;
        }

        .requirements ul {
            margin: 10px 0;
            padding-left: 20px;
        }

        .requirements li {
            margin-bottom: 8px;
            line-height: 1.5;
        }

        .owner-section {
            background-color: #f0fdf4;
            border: 1px solid #22c55e;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .owner-section h4 {
            margin-top: 0;
            color: #15803d;
            font-size: 16px;
        }

        .authorized-section {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .authorized-section h4 {
            margin-top: 0;
            color: #92400e;
            font-size: 16px;
        }

        .qr-section {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
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
        <div class="title">Document Request Claim Slip</div>
        <div class="reference">Reference Number: {{ $reference_number }}</div>
    </div>

    <div class="section">
        <div class="section-title">Document Request Details</div>
        <div class="grid">
            <div class="row">
                <div class="col label">Reference Number:</div>
                <div class="col value" style="font-family: monospace; font-weight: bold;">{{ $reference_number }}</div>
            </div>
            <div class="row">
                <div class="col label">Document Type:</div>
                <div class="col value">{{ $service->title }}</div>
            </div>
            <div class="row">
                <div class="col label">Office:</div>
                <div class="col value">{{ $office->name }}</div>
            </div>
            @if(isset($requested_date))
            <div class="row">
                <div class="col label">Request Date:</div>
                <div class="col value">{{ \Carbon\Carbon::parse($requested_date)->format('M d, Y') }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">Requester Information</div>
        <div class="grid">
            <div class="row">
                <div class="col label">Requested By:</div>
                <div class="col value">{{ $requestor_name }}</div>
            </div>
            @if($request_for === 'someone_else')
            <div class="row">
                <div class="col label">Document For:</div>
                <div class="col value">{{ $document_owner_name }}</div>
            </div>
            @endif
            <div class="row">
                <div class="col label">Request Type:</div>
                <div class="col value">{{ $request_for === 'myself' ? 'Owner' : 'Authorized Person' }}</div>
            </div>
        </div>
    </div>

    <div class="requirements">
        <h4>Bring the following requirements to claim your requested document:</h4>

        @if($request_for === 'myself')
        <div class="owner-section">
            <h4>Owner</h4>
            <ul>
                <li>Valid ID (photocopy is accepted make sure it includes the back with signature)</li>
            </ul>
        </div>
        @else
        <div class="authorized-section">
            <h4>Authorized Person</h4>
            <ul>
                <li>Valid ID of Requested document (Physical ID)</li>
                <li>Valid ID of Authorized Person (photocopy is accepted make sure it includes the back with signature)</li>
                <li>Claim slip (this document)</li>
                <li>Authorization letter</li>
            </ul>
        </div>
        @endif
    </div>

    <div class="qr-section">
        <div style="text-align: center; margin-bottom: 10px;">
            <strong>Reference Number: {{ $reference_number }}</strong>
        </div>
        <div style="text-align: center; font-size: 12px; color: #666;">
            Present this reference number at the office for verification
        </div>
    </div>

    <div class="requirements" style="margin-top: 30px;">
        <h4>Important Notes:</h4>
        <ul>
            <li>Please bring this claim slip when claiming your document.</li>
            <li>Ensure all required documents are complete before visiting the office.</li>
            <li>Please proceed to the <strong>{{ strtoupper($office->name) }}</strong> office to claim your document.</li>
            <li>For questions or concerns, please contact the office directly.</li>
        </ul>
    </div>
</body>

</html>

