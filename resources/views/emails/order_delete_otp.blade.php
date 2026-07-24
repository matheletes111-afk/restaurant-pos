<!DOCTYPE html>
<html>
<head>
    <title>Order Deletion Verification OTP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: 'Public Sans', 'Segoe UI', sans-serif;">
    <div style="max-width:600px; margin:40px auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
        <div style="background-color: #ffffff; padding: 30px; text-align: center; border-bottom: 1px solid #e2e8f0;">
            <img src="{{ asset('logo.png') }}" alt="Logo" style="max-height: 50px; width: auto; display: inline-block;">
        </div>
        <div style="padding: 40px 30px; color: #1e293b;">
            <h2 style="margin-top: 0; margin-bottom: 20px; font-size: 20px; font-weight: 800; color: #ef4444; text-align: center; text-transform: uppercase; letter-spacing: 0.05em;">
                Order Deletion Authorization
            </h2>
            <p style="font-size: 15px; line-height: 1.6; color: #475569; margin-bottom: 20px;">
                Hello <strong>{{ $data['name'] }}</strong>,
            </p>
            <p style="font-size: 15px; line-height: 1.6; color: #475569; margin-bottom: 20px;">
                A request has been made to delete Order <strong>#{{ $data['order_uid'] }}</strong>.
            </p>
            <p style="font-size: 15px; line-height: 1.6; color: #475569; margin-bottom: 20px; background-color: #f8fafc; padding: 12px; border-radius: 6px; border-left: 4px solid #cbd5e1;">
                <strong>Reason/Remarks:</strong><br>
                <em>{{ $data['remarks'] }}</em>
            </p>
            <p style="font-size: 15px; line-height: 1.6; color: #475569; margin-bottom: 30px;">
                To authorize this deletion, please use the following one-time verification code (OTP):
            </p>
            
            <div style="text-align: center; margin: 30px 0;">
                <span style="font-size: 32px; font-weight: 800; letter-spacing: 6px; color: #ef4444; background-color: #fef2f2; padding: 16px 32px; border-radius: 12px; border: 2px dashed #ef4444; display: inline-block;">
                    {{ $data['otp'] }}
                </span>
            </div>
            
            <p style="font-size: 14px; line-height: 1.5; color: #64748b; margin-top: 30px; background-color: #f8fafc; padding: 15px; border-radius: 8px; border-left: 4px solid #ef4444;">
                <strong>Note:</strong> This verification code is valid for 10 minutes. If you did not authorize this action, please change your login credentials or contact system support immediately.
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
