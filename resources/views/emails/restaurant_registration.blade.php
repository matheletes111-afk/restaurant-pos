<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Registration Successful</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 0;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .greeting strong {
            color: #667eea;
            font-size: 20px;
        }
        .credentials-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 25px 0;
            border-radius: 5px;
        }
        .credential-item {
            margin-bottom: 15px;
            padding: 10px;
            background: white;
            border-radius: 5px;
        }
        .credential-label {
            font-weight: bold;
            color: #667eea;
            display: inline-block;
            width: 100px;
        }
        .credential-value {
            color: #333;
            font-family: monospace;
            font-size: 14px;
        }
        .restaurant-details {
            background-color: #e8f4f8;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .restaurant-details h3 {
            margin-top: 0;
            color: #2c3e50;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e0e0e0;
        }
        @media only screen and (max-width: 600px) {
            .content {
                padding: 20px;
            }
            .credential-label {
                display: block;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to {{ config('app.name') }}!</h1>
            <p>Your Restaurant Registration is Successful</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                Dear <strong>{{ $user->name }}</strong>,
            </div>
            
            <p>Thank you for registering your restaurant with us! We are excited to have you on board. Your account has been successfully created.</p>
            
            <div class="restaurant-details">
                <h3>🏢 Restaurant Information</h3>
                <p><strong>Restaurant Name:</strong> {{ $restaurant->name }}</p>
                <p><strong>Address:</strong> {{ $restaurant->address }}</p>
                <p><strong>Pincode:</strong> {{ $restaurant->pincode }}</p>
            </div>
            
            <div class="credentials-box">
                <h3>🔐 Your Login Credentials</h3>
                <div class="credential-item">
                    <span class="credential-label">Email:</span>
                    <span class="credential-value">{{ $user->email }}</span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Password:</span>
                    <span class="credential-value">{{ $plainPassword }}</span>
                </div>
            </div>
            
            <div class="warning">
                ⚠️ <strong>Important:</strong> Please change your password after your first login for security purposes. Keep your credentials safe and do not share them with anyone.
            </div>
            
            <center>
                <a href="{{ url('/login') }}" class="button">Login to Your Dashboard</a>
            </center>
            
            <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>
            
            <p>Best regards,<br>
            <strong>{{ config('app.name') }} Team</strong></p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>