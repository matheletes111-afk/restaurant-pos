<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Support Ticket</title>
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
        .alert-badge {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            display: inline-block;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .priority-high {
            background: #dc2626;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }
        .priority-medium {
            background: #f59e0b;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }
        .priority-low {
            background: #10b981;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }
        .priority-urgent {
            background: #7c3aed;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .info-item {
            margin-bottom: 12px;
            padding: 8px;
            background: white;
            border-radius: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #667eea;
            display: inline-block;
            width: 120px;
        }
        .info-value {
            color: #333;
        }
        .message-box {
            background-color: #fef3c7;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #f59e0b;
        }
        .message-box h3 {
            margin-top: 0;
            color: #92400e;
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
            .info-label {
                display: block;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Support Ticket</h1>
            <p>A new support request has been submitted</p>
        </div>
        
        <div class="content">
            <div style="text-align: center;">
                <div class="alert-badge">
                    <i class="fas fa-ticket-alt"></i> New Ticket Alert
                </div>
            </div>
            
            <div class="info-box">
                <h3>📋 Ticket Information</h3>
                <div class="info-item">
                    <span class="info-label">Ticket Number:</span>
                    <span class="info-value"><strong>#{{ $ticket->ticket_no }}</strong></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Priority:</span>
                    <span class="info-value">
                        @if($ticket->priority == 'URGENT')
                            <span class="priority-urgent">Urgent</span>
                        @elseif($ticket->priority == 'HIGH')
                            <span class="priority-high">High</span>
                        @elseif($ticket->priority == 'MEDIUM')
                            <span class="priority-medium">Medium</span>
                        @else
                            <span class="priority-low">Low</span>
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Subject:</span>
                    <span class="info-value">{{ $ticket->subject }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="info-value"><span style="color: #f59e0b;">New</span></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Created At:</span>
                    <span class="info-value">{{ $ticket->created_at->format('d M Y, h:i A') }}</span>
                </div>
            </div>
            
            <div class="info-box">
                <h3>🏢 Restaurant Information</h3>
                <div class="info-item">
                    <span class="info-label">Restaurant Name:</span>
                    <span class="info-value"><strong>{{ $restaurant->name ?? 'N/A' }}</strong></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Owner Name:</span>
                    <span class="info-value">{{ $user->name ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $user->email ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Phone:</span>
                    <span class="info-value">{{ $user->phone ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Address:</span>
                    <span class="info-value">{{ $restaurant->address ?? 'N/A' }}</span>
                </div>
            </div>
            
            <div class="message-box">
                <h3>📝 Message</h3>
                <p style="white-space: pre-wrap;">{{ $ticket->message }}</p>
            </div>
            
            <center>
                <a href="{{ url('/admin/support/ticket/' . $ticket->id) }}" class="button">
                    <i class="fas fa-eye"></i> View & Reply to Ticket
                </a>
            </center>
            
            <p style="margin-top: 20px; font-size: 12px; color: #666; text-align: center;">
                Please respond to this ticket as soon as possible. The customer is waiting for assistance.
            </p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>This is an automated notification, please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>