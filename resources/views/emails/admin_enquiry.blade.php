<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Website Enquiry Received</title>
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
        .header h1 {
            margin: 15px 0 0;
            font-size: 22px;
            font-weight: 700;
            color: #ff6a00;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #555;
        }
        .content {
            padding: 35px 30px;
        }
        .intro {
            font-size: 16px;
            color: #4b5563;
            margin-bottom: 25px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .details-table th, .details-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .details-table th {
            background-color: #f9fafb;
            color: #4b5563;
            font-weight: 600;
            width: 35%;
        }
        .details-table td {
            color: #1f2937;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            background-color: #ffebeb;
            color: #ff6a00;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="{{ asset('logo.png') }}" alt="Bill & Bite Logo">
            <h1>New Website Enquiry</h1>
            <p>Bill & Bite Restaurant POS System</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="intro">
                Hello Admin,
                <br><br>
                A new enquiry / demo request has been submitted from the landing page. Here are the details of the submission:
            </div>

            <!-- Details Table -->
            <table class="details-table">
                <tr>
                    <th>Full Name</th>
                    <td><strong>{{ $details['full_name'] }}</strong></td>
                </tr>
                <tr>
                    <th>Email Address</th>
                    <td><a href="mailto:{{ $details['email_address'] }}">{{ $details['email_address'] }}</a></td>
                </tr>
                <tr>
                    <th>Phone Number</th>
                    <td>{{ $details['phone_number'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Restaurant Name</th>
                    <td>{{ $details['restaurant_name'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Source</th>
                    <td><span class="badge">{{ $details['source'] ?? 'Direct / Not Specified' }}</span></td>
                </tr>
                <tr>
                    <th>Submitted At</th>
                    <td>{{ date('Y-m-d H:i:s') }}</td>
                </tr>
            </table>

            <p style="font-size: 14px; color: #6b7280; text-align: center; margin: 0;">
                Please follow up with this prospect as soon as possible.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is an automated system notification from Bill & Bite POS.</p>
        </div>
    </div>
</body>
</html>
