<!DOCTYPE html>
<html>
<head>
    <title>Two-Factor Authentication OTP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: 'Public Sans', 'Segoe UI', sans-serif;">
    <div style="max-width:600px; margin:40px auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
        <div style="background: linear-gradient(135deg, #009d1a 0%, #00bc20 100%); padding: 30px; text-align: center;">
            <img src="{{ asset('logo.png') }}" alt="Logo" style="max-height: 60px; width: auto;">
        </div>
        <div style="padding: 40px 30px; color: #1e293b;">
            <h2 style="margin-top: 0; margin-bottom: 20px; font-size: 20px; font-weight: 800; color: #0f172a; text-align: center; text-transform: uppercase; letter-spacing: 0.05em;">
                Two-Factor Authentication
            </h2>
            <p style="font-size: 15px; line-height: 1.6; color: #475569; margin-bottom: 20px;">
                Hello <strong>{{ $data['name'] }}</strong>,
            </p>
            <p style="font-size: 15px; line-height: 1.6; color: #475569; margin-bottom: 30px;">
                A sign-in attempt was detected on your account. To proceed and complete your authentication, please use the following one-time verification code (OTP):
            </p>
            
            <div style="text-align: center; margin: 30px 0;">
                <span style="font-size: 32px; font-weight: 800; letter-spacing: 6px; color: #009d1a; background-color: #f0fdf4; padding: 16px 32px; border-radius: 12px; border: 2px dashed #009d1a; display: inline-block;">
                    {{ $data['otp'] }}
                </span>
            </div>
            
            <p style="font-size: 14px; line-height: 1.5; color: #64748b; margin-top: 30px; background-color: #f8fafc; padding: 15px; border-radius: 8px; border-left: 4px solid #009d1a;">
                <strong>Note:</strong> This verification code is valid for 10 minutes. If you did not initiate this login request, please ignore this email or contact your administrator immediately.
            </p>
        </div>
        <div style="background-color: #f1f5f9; padding: 20px; text-align: center; border-top: 1px solid #e2e8f0;">
            <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                &copy; {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
