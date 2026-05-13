<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - Ticket #{{ $ticket->ticket_no }}</title>
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
        }
        .message-left .message-bubble {
            background: linear-gradient(135deg, #FF6B35, #E85D2C);
            color: white;
            border-top-left-radius: 5px;
        }
        .message-right .message-bubble {
            background: white;
            border: 1px solid #e2e8f0;
            border-top-right-radius: 5px;
        }
        .ticket-info {
            background: #f1f5f9;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .status-btn { margin: 2px; }
    </style>
</head>
<body>
@include('includes.sidebar')

<div class="pc-container">
    <div class="pc-content">
        <div class="page-header">
            <h5 class="m-b-10">Ticket #{{ $ticket->ticket_no }}</h5>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Ticket Info -->
        <div class="ticket-info">
            <div class="row">
                <div class="col-md-6">
                    <strong>Restaurant:</strong> {{ $ticket->restaurant->name ?? 'N/A' }}<br>
                    <strong>Subject:</strong> {{ $ticket->subject }}<br>
                    <strong>Priority:</strong> <span class="priority-badge priority-{{ $ticket->priority }}">{{ $ticket->priority }}</span>
                </div>
                <div class="col-md-6">
                    <strong>Status:</strong> 
                    <!-- -->
                    <strong>Created:</strong> {{ $ticket->created_at->format('d M Y, h:i A') }}
                </div>
            </div>
        </div>

        <!-- Assign to Admin -->
<!--         <div class="card mb-3">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.support.ticket.assign', $ticket->id) }}" class="form-inline">
                    @csrf
                    <label class="mr-2">Assign to:</label>
                    <select name="assigned_to" class="form-control mr-2">
                        <option value="">-- Select Admin --</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" {{ $ticket->assigned_to == $admin->id ? 'selected' : '' }}>
                                {{ $admin->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">Assign</button>
                </form>
            </div>
        </div> -->

        <!-- Chat Messages -->
        <div class="card">
            <div class="card-header">
                <h5>Conversation</h5>
            </div>
            <div class="card-body">
                <div class="chat-container">
                    <!-- Initial Ticket Message -->
                    <div class="message-left">
                        <div class="message-bubble">
                            <strong>{{ $ticket->creator->name ?? 'Restaurant' }}</strong><br>
                            {{ $ticket->message }}
                            <div class="message-meta">{{ $ticket->created_at->format('d M Y, h:i A') }}</div>
                        </div>
                    </div>
                    
                    <!-- Comments -->
                    @foreach($ticket->comments as $comment)
                        @if($comment->user_type == 'ADMIN')
                            <div class="message-right">
                                <div class="message-bubble">
                                    <strong><i class="fas fa-user-shield"></i> You (Admin)</strong><br>
                                    {{ $comment->comment }}
                                    @if($comment->attachment)
                                        <div><a href="{{ asset($comment->attachment) }}" target="_blank">View Attachment</a></div>
                                    @endif
                                    <div class="message-meta">{{ $comment->created_at->format('d M Y, h:i A') }}</div>
                                </div>
                            </div>
                        @else
                            <div class="message-left">
                                <div class="message-bubble">
                                    <strong>{{ $ticket->restaurant->name ?? 'Restaurant' }}</strong><br>
                                    {{ $comment->comment }}
                                    @if($comment->attachment)
                                        <div><a href="{{ asset($comment->attachment) }}" target="_blank" class="text-white">View Attachment</a></div>
                                    @endif
                                    <div class="message-meta">{{ $comment->created_at->format('d M Y, h:i A') }}</div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                
                <!-- Add Reply Form -->
                <hr>
                @include('includes.message')
                <form method="POST" action="{{ route('admin.support.comment.add', $ticket->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Add Reply</label>
                        <textarea name="comment" class="form-control" rows="3" required placeholder="Type your reply..."></textarea>
                    </div>
                    <!-- <div class="form-group">
                        <label>Attachment (Optional)</label>
                        <input type="file" name="attachment" class="form-control-file">
                    </div> -->
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Reply
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@include('includes.script')
</body>
</html>