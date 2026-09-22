<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #2d3748;
            margin: 0;
            padding: 0;
            background-color: #f7fafc;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f7fafc;
            padding: 30px 0 40px 0;
        }
        .main-table {
            background-color: #ffffff;
            margin: 0 auto;
            width: 600px;
            max-width: 600px;
            border-spacing: 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid #edf2f7;
        }
        .header-bar {
            background: linear-gradient(135deg, #ff6a00 0%, #ee0979 100%);
            height: 6px;
            width: 100%;
        }
        .header {
            padding: 28px 36px 20px 36px;
            background-color: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            text-align: left;
        }
        .logo {
            max-height: 44px;
            width: auto;
            display: inline-block;
        }
        .header-title-badge {
            display: inline-block;
            background-color: #fff5eb;
            color: #ff6a00;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 4px 10px;
            border-radius: 20px;
            margin-top: 12px;
        }
        .content {
            padding: 32px 36px 28px 36px;
            font-size: 15px;
            color: #334155;
            line-height: 1.7;
        }
        .greeting-card {
            background-color: #f8fafc;
            border-left: 4px solid #ff6a00;
            padding: 14px 18px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 24px;
            font-size: 15px;
        }
        .greeting-card strong {
            color: #0f172a;
        }
        .greeting-card .restaurant-tag {
            color: #64748b;
            font-size: 13px;
        }
        .body-text {
            color: #334155;
            font-size: 15px;
            line-height: 1.7;
        }
        .body-text h1, .body-text h2, .body-text h3 {
            color: #0f172a;
            margin-top: 24px;
            margin-bottom: 12px;
            line-height: 1.3;
        }
        .body-text p {
            margin: 0 0 16px 0;
        }
        .body-text ul, .body-text ol {
            padding-left: 24px;
            margin-bottom: 18px;
        }
        .body-text li {
            margin-bottom: 6px;
        }
        .body-text a {
            color: #ff6a00;
            text-decoration: underline;
        }
        .body-text img {
            max-width: 100% !important;
            height: auto !important;
            border-radius: 8px;
            margin: 12px 0;
        }
        .cta-container {
            text-align: center;
            margin: 30px 0 10px 0;
            padding-top: 10px;
        }
        .cta-button {
            background: linear-gradient(135deg, #ff6a00 0%, #ff8c00 100%);
            color: #ffffff !important;
            text-decoration: none !important;
            padding: 13px 30px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            display: inline-block;
            box-shadow: 0 4px 12px rgba(255, 106, 0, 0.25);
            letter-spacing: 0.3px;
        }
        .divider {
            border-top: 1px solid #f1f5f9;
            margin: 28px 0;
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px 36px 28px 36px;
            text-align: center;
            border-top: 1px solid #edf2f7;
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.6;
        }
        .footer a {
            color: #64748b;
            text-decoration: underline;
        }
        .footer p {
            margin: 4px 0;
        }
        @media only screen and (max-width: 620px) {
            .main-table {
                width: 94% !important;
            }
            .header, .content, .footer {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="main-table" align="center" cellpadding="0" cellspacing="0">
            <!-- Top Gradient Bar -->
            <tr>
                <td>
                    <div class="header-bar"></div>
                </td>
            </tr>

            <!-- Header with Logo -->
            <tr>
                <td class="header">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                <img src="{{ asset('logo.png') }}" alt="{{ config('app.name', 'Bill&Bite POS') }}" class="logo">
                            </td>
                            <td align="right">
                                <span class="header-title-badge">Official Announcement</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Main Content Area -->
            <tr>
                <td class="content">
                    <!-- Personalized Greeting -->
                    <div class="greeting-card">
                        <div>Hello <strong>{{ $recipientData['owner_name'] ?? 'Valued Partner' }}</strong>,</div>
                        <div class="restaurant-tag">Partner Restaurant: <strong>{{ $recipientData['restaurant_name'] ?? 'Your Restaurant' }}</strong></div>
                    </div>

                    <!-- TinyMCE Rich Content -->
                    <div class="body-text">
                        {!! $emailContent !!}
                    </div>

                    <!-- Portal Access CTA -->
                    <div class="cta-container">
                        <a href="{{ url('/login') }}" class="cta-button" target="_blank">
                            Go to Restaurant Portal &rarr;
                        </a>
                    </div>
                </td>
            </tr>

            <!-- Footer -->
            <tr>
                <td class="footer">
                    <p><strong>{{ config('app.name', 'Bill&Bite') }}</strong> &bull; Complete Restaurant Management & POS Platform</p>
                    <p>This notification was sent to <strong>{{ $recipientData['email'] ?? '' }}</strong> regarding your restaurant account.</p>
                    <p style="margin-top: 10px; font-size: 11px; color: #cbd5e1;">
                        &copy; {{ date('Y') }} {{ config('app.name', 'Bill&Bite') }}. All rights reserved.
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
