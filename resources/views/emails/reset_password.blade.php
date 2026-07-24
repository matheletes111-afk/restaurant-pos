<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password Email</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid #eef2f6;
        }
        .header {
            background-color: #ffffff;
            padding: 30px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }
        .header img {
            max-height: 50px;
            width: auto;
            display: inline-block;
        }
        .content {
            padding: 40px 35px;
        }
        .otp-box {
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 6px;
            color: #ff6a00;
            background-color: #fff8f5;
            padding: 16px 32px;
            border-radius: 12px;
            border: 2px dashed #ff6a00;
            display: inline-block;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            font-size: 13px;
            color: #888;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('logo.png') }}" alt="Bill & Bite Logo">
        </div>
        <div class="content">
            <p style="font-size: 16px; margin-bottom: 20px; color: #111;">
                Hello <strong>{{ $data['name'] }}</strong>,
            </p>
            <p style="font-size: 16px; color: #555;">
                We received a request to reset your password. Please use the following One-Time Password (OTP) to proceed:
            </p>
            <div class="otp-box">
                <span class="otp-code">{{ $data['email_vcode'] }}</span>
            </div>
            <p style="font-size: 14px; color: #777;">
                This OTP is valid for a limited time. If you did not request a password reset, please ignore this email.
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Bill & Bite. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
