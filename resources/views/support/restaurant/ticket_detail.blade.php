<!DOCTYPE html>
<html lang="en">
<head>
    <title>Ticket #{{ $ticket->ticket_no }}</title>
    @include('includes.style')
    <style>
        .chat-container {
            max-height: 500px;
            overflow-y: auto;
            padding: 15px;
            background: #f8fafc;
            border-radius: 10px;
        }
        .message-left {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 20px;
        }
        .message-right {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        .message-bubble {
            max-width: 70%;
            padding: 12px 15px;
            border-radius: 15px;
            position: relative;
        }
        .message-left .message-bubble {
            background: white;
            border: 1px solid #e2e8f0;
            border-top-left-radius: 5px;
        }
        .message-right .message-bubble {
            background: linear-gradient(135deg, #FF6B35, #E85D2C);
            color: white;
            border-top-right-radius: 5px;
        }
        .message-meta {
            font-size: 0.7rem;
            margin-top: 5px;
            opacity: 0.7;
        }
        .attachment-link {
            display: inline-block;
            margin-top: 5px;
            font-size: 0.75rem;
        }
        .ticket-info {
            background: #f1f5f9;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .resolve-btn {
            background: #10b981;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
@include('includes.sidebar')

<div class="pc-container">
    <div class="pc-content">
        <div class="page-header">
            <h5 class="m-b-10">Ticket #{{ $ticket->ticket_no }}</h5>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('restaurant.support.tickets') }}">My Tickets</a></li>
                <li class="breadcrumb-item active">Ticket Details</li>
            </ul>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Ticket Info -->
        <div class="ticket-info">
            <div class="row">
                <div class="col-md-6">
                    <strong>Subject:</strong> {{ $ticket->subject }}<br>
                    <strong>Status:</strong> {!! $ticket->statusBadge !!}<br>
                    <strong>Priority:</strong> <span class="priority-badge priority-{{ $ticket->priority }}">{{ $ticket->priority }}</span>
                </div>
                <div class="col-md-6 text-md-right">
                    <strong>Created:</strong> {{ $ticket->created_at->format('d M Y, h:i A') }}<br>
                    <strong>Last Updated:</strong> {{ $ticket->updated_at->format('d M Y, h:i A') }}
                </div>
            </div>
        </div>

        <!-- Chat Messages -->
        <div class="card">
            <div class="card-header">
                <h5>Conversation</h5>
            </div>
            <div class="card-body">
                <div class="chat-container">
                    <!-- Initial Ticket Message -->
                    <div class="message-right">
                        <div class="message-bubble">
                            <strong>You</strong><br>
                            {{ $ticket->message }}
                            <div class="message-meta">{{ $ticket->created_at->format('d M Y, h:i A') }}</div>
                        </div>
                    </div>
                    
                    <!-- Comments -->
                    @foreach($ticket->comments as $comment)
                        @if($comment->user_type == 'ADMIN')
                            <div class="message-left">
                                <div class="message-bubble">
                                    <strong><i class="fas fa-user-shield"></i> Support Team</strong><br>
                                    {{ $comment->comment }}
                                    @if($comment->attachment)
                                        <div class="attachment-link">
                                            <a href="{{ asset($comment->attachment) }}" target="_blank" class="text-primary">
                                                <i class="fas fa-paperclip"></i> View Attachment
                                            </a>
                                        </div>
                                    @endif
                                    <div class="message-meta">{{ $comment->created_at->format('d M Y, h:i A') }}</div>
                                </div>
                            </div>
                        @else
                            <div class="message-right">
                                <div class="message-bubble">
                                    <strong>You</strong><br>
                                    {{ $comment->comment }}
                                    @if($comment->attachment)
                                        <div class="attachment-link">
                                            <a href="{{ asset($comment->attachment) }}" target="_blank" class="text-white">
                                                <i class="fas fa-paperclip"></i> View Attachment
                                            </a>
                                        </div>
                                    @endif
                                    <div class="message-meta">{{ $comment->created_at->format('d M Y, h:i A') }}</div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                
                <!-- Add Comment Form -->
                @if($ticket->status != 'RESOLVED')
                <hr>
                <form method="POST" action="{{ route('restaurant.support.comment.add', $ticket->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Add Reply</label>
                        <textarea name="comment" class="form-control" rows="3" required placeholder="Type your message here..."></textarea>
                    </div>
                    <!-- <div class="form-group">
                        <label>Attachment (Optional)</label>
                        <input type="file" name="attachment" class="form-control-file">
                    </div> -->
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Reply
                    </button>
                </form>
                @endif
            </div>
        </div>
        
        <!-- Resolve Button -->
        @if($ticket->status != 'RESOLVED')
        <div class="text-right mt-3" style="padding-bottom: 50px;">
            <form method="POST" action="{{ route('restaurant.support.ticket.resolve', $ticket->id) }}">
                @csrf
                <button type="submit" class="resolve-btn" onclick="return confirm('Mark this ticket as resolved?')">
                    <i class="fas fa-check-circle"></i> Mark as Resolved
                </button>
            </form>
        </div>
        @endif
    </div>
</div>

@include('includes.script')
</body>
</html>