<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Your Enquiry</title>
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
        .header p {
            margin: 10px 0 0;
            font-size: 14px;
            color: #555;
        }
        .content {
            padding: 40px 35px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #111;
        }
        .greeting strong {
            color: #ff6a00;
        }
        .message-body {
            font-size: 16px;
            color: #555;
            margin-bottom: 30px;
        }
        .divider {
            height: 1px;
            background-color: #eee;
            margin: 30px 0;
        }
        .button-wrapper {
            text-align: center;
            margin: 35px 0;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #ff6a00 0%, #ff8c42 100%);
            color: white !important;
            padding: 14px 35px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(255, 106, 0, 0.25);
            transition: all 0.3s ease;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            font-size: 13px;
            color: #888;
            border-top: 1px solid #eee;
        }
        .footer a {
            color: #ff6a00;
            text-decoration: none;
        }
        .social-links {
            margin-top: 15px;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #888;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="{{ asset('logo.png') }}" alt="Bill & Bite Logo">
            <p>Complete Restaurant Management System</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Hello <strong>{{ $details['full_name'] }}</strong>,
            </div>
            
            <div class="message-body">
                <p>Thank you for reaching out to us! We have received your enquiry regarding Bill & Bite restaurant POS.</p>
                <p>Our sales and support team is reviewing your details, and one of our experts will contact you soon to assist you and discuss your requirements.</p>
                {{-- <p>In the meantime, you can explore more features and manage your operations on our main website.</p> --}}
            </div>

            <div class="button-wrapper">
                <a href="https://billnbite.com/" class="button" target="_blank">Visit Our Website</a>
            </div>

            <div class="divider"></div>

            <p style="font-size: 14px; color: #777; margin: 0; text-align: center;">
                If you have any urgent questions, feel free to reply directly to this email or call us.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© {{ date('Y') }} Bill & Bite. All rights reserved.</p>
            <p>Siliguri, India | <a href="mailto:info@billnbite.com">info@billnbite.com</a></p>
        </div>
    </div>
</body>
</html>
