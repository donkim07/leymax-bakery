<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ config('app.name') }}</title>
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
        .credentials {
            background-color: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
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
            <h1>Welcome to {{ config('app.name') }}!</h1>
        </div>
        <div class="content">
            <h2>Hello {{ $name }},</h2>
            <p>Welcome to {{ config('app.name') }}! Your account has been created successfully.</p>
            
            <p>Here are your login credentials:</p>
            <div class="credentials">
                <p><strong>Username:</strong> {{ $username }}</p>
                <p><strong>Email:</strong> {{ $email }}</p>
                <p><strong>Password:</strong> {{ $password }}</p>
                <p><small>Please change your password after the first login for security reasons.</small></p>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $login_url }}" class="btn">Login Now</a>
            </div>
            
            <p>If you have any questions or need assistance, please feel free to contact our support team.</p>
            
            <p>Regards,<br>{{ config('app.name') }} Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>