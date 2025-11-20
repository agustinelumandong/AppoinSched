<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Login Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #2563eb;
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 30px 20px;
        }

        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #333;
        }

        .message {
            margin-bottom: 30px;
            color: #555;
        }

        .code-box {
            background-color: #f0f9ff;
            border: 2px solid #2563eb;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }

        .code {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 4px;
            color: #2563eb;
            font-family: 'Courier New', monospace;
        }

        .instructions {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
        }

        .instructions h3 {
            margin-top: 0;
            color: #92400e;
        }

        .instructions ol {
            margin: 10px 0;
            padding-left: 20px;
        }

        .instructions li {
            margin-bottom: 8px;
            color: #78350f;
        }

        .button {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
        }

        .button:hover {
            background-color: #1d4ed8;
        }

        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            border-top: 1px solid #e5e7eb;
        }

        .warning {
            background-color: #fee2e2;
            border-left: 4px solid #ef4444;
            padding: 15px;
            margin: 20px 0;
            color: #991b1b;
        }

        .warning strong {
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>

        <div class="content">
            <div class="greeting">
                Hello {{ $user->first_name }} {{ $user->last_name }}!
            </div>

            <div class="message">
                <p>Your account has been created. Please use the login code below to access your account for the first time.</p>
            </div>

            <div class="code-box">
                <div style="margin-bottom: 10px; color: #64748b; font-size: 14px;">Your Login Code:</div>
                <div class="code">{{ $loginCode }}</div>
            </div>

            <div class="instructions">
                <h3>How to use your login code:</h3>
                <ol>
                    <li>Go to the login page using the button below</li>
                    <li>Enter your email address: <strong>{{ $user->email }}</strong></li>
                    <li>Enter the login code shown above</li>
                    <li>After logging in, you will be asked to set your password</li>
                </ol>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('login', absolute: true) }}" class="button">Go to Login Page</a>
            </div>

            <div class="warning">
                <strong>Important:</strong>
                <p>This login code will expire in 7 days. After you set your password, you can use your email and password to log in.</p>
            </div>
        </div>

        <div class="footer">
            <p>This is an automated message from {{ config('app.name') }}. Please do not reply to this email.</p>
            <p>If you did not request this account, please contact support immediately.</p>
        </div>
    </div>
</body>

</html>

