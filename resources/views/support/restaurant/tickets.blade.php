<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Support Tickets</title>
    @include('includes.style')
    <style>
        .ticket-card {
            border-left: 4px solid;
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        .ticket-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .status-NEW { border-left-color: #f59e0b; }
        .status-IN_PROGRESS { border-left-color: #3b82f6; }
        .status-RESOLVED { border-left-color: #10b981; }
        .priority-badge { padding: 3px 8px; border-radius: 5px; font-size: 0.7rem; font-weight: 600; }
        .priority-LOW { background: #e2e8f0; color: #475569; }
        .priority-MEDIUM { background: #dbeafe; color: #1e40af; }
        .priority-HIGH { background: #fef3c7; color: #92400e; }
        .priority-URGENT { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
@include('includes.sidebar')

<div class="pc-container">
    <div class="pc-content">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="m-b-10">My Support Tickets</h5>
            </div>
            <a href="{{ route('restaurant.support.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Ticket
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @forelse($tickets as $ticket)
        <a href="{{ route('restaurant.support.ticket.view', $ticket->id) }}" style="text-decoration: none; color: inherit;">
            <div class="card ticket-card status-{{ $ticket->status }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">#{{ $ticket->ticket_no }} - {{ $ticket->subject }}</h6>
                            <small class="text-muted">{{ $ticket->created_at->format('d M Y, h:i A') }}</small>
                        </div>
                        <div class="text-right">
                            <div class="mb-1">{!! $ticket->statusBadge !!}</div>
                            <div><span class="priority-badge priority-{{ $ticket->priority }}">{{ $ticket->priority }}</span></div>
                        </div>
                    </div>
                    <p class="mt-2 mb-0 text-muted">{{ Str::limit($ticket->message, 100) }}</p>
                    <div class="mt-2">
                        <small><i class="fas fa-comment"></i> {{ $ticket->comments->count() }} replies</small>
                    </div>
                </div>
            </div>
        </a>
        @empty
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-ticket-alt" style="font-size: 48px; color: #cbd5e1;"></i>
                <h5 class="mt-3">No Tickets Found</h5>
                <p class="text-muted">Create your first support ticket</p>
                <a href="{{ route('restaurant.support.create') }}" class="btn btn-primary">Create Ticket</a>
            </div>
        </div>
        @endforelse

        {{ $tickets->links() }}
    </div>
</div>

@include('includes.script')
</body>
</html>