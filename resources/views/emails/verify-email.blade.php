<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email Address</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4154f1;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 5px 5px;
            border: 1px solid #ddd;
            border-top: none;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #777;
        }
        .btn {
            display: inline-block;
            background-color: #4154f1;
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 4px;
            margin: 15px 0;
            font-weight: bold;
        }
        h1 {
            margin: 0;
            font-size: 24px;
        }
        h2 {
            font-size: 20px;
            margin-top: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Verify Your Email Address</h1>
        </div>
        <div class="content">
            <h2>Hello {{ $name }}!</h2>
            <p>Thank you for registering with {{ config('app.name') }}. Please click the button below to verify your email address.</p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $verificationUrl }}" class="btn">Verify Email Address</a>
            </div>
            
            <p>This verification link will expire in {{ config('auth.passwords.users.expire', 60) }} minutes.</p>
            <p>If you did not create an account, no further action is required.</p>
            
            <p>If you're having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web browser:</p>
            <p style="background-color: #f0f0f0; padding: 10px; word-break: break-all;">{{ $verificationUrl }}</p>
            
            <p>Regards,<br>{{ config('app.name') }} Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>If you have any questions, please contact our support team.</p>
        </div>
    </div>
</body>
</html> 