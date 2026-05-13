<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - Support Tickets</title>
    @include('includes.style')
    <style>
        .ticket-row:hover { background: #f8fafc; cursor: pointer; }
        .status-NEW { background: #fef3c7; color: #92400e; }
        .status-IN_PROGRESS { background: #dbeafe; color: #1e40af; }
        .status-RESOLVED { background: #d1fae5; color: #065f46; }
        .stats-card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
        .stats-number { font-size: 2rem; font-weight: 700; }
    </style>
</head>
<body>
@include('includes.sidebar')

<div class="pc-container">
    <div class="pc-content">
        <div class="page-header">
            <h5 class="m-b-10">Support Tickets Management</h5>
        </div>

        <!-- Stats -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number text-warning">{{ $statusCounts['new'] }}</div>
                    <div>New Tickets</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number text-info">{{ $statusCounts['in_progress'] }}</div>
                    <div>In Progress</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number text-success">{{ $statusCounts['resolved'] }}</div>
                    <div>Resolved</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row">
                    <div class="col-md-4">
                        <select name="status" class="form-control">
                            <option value="all">All Status</option>
                            <option value="NEW" {{ request('status') == 'NEW' ? 'selected' : '' }}>New</option>
                            <option value="IN_PROGRESS" {{ request('status') == 'IN_PROGRESS' ? 'selected' : '' }}>In Progress</option>
                            <option value="RESOLVED" {{ request('status') == 'RESOLVED' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="priority" class="form-control">
                            <option value="all">All Priority</option>
                            <option value="LOW" {{ request('priority') == 'LOW' ? 'selected' : '' }}>Low</option>
                            <option value="MEDIUM" {{ request('priority') == 'MEDIUM' ? 'selected' : '' }}>Medium</option>
                            <option value="HIGH" {{ request('priority') == 'HIGH' ? 'selected' : '' }}>High</option>
                            <option value="URGENT" {{ request('priority') == 'URGENT' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.support.tickets') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tickets Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Ticket #</th>
                                <th>Restaurant</th>
                                <th>Subject</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $ticket)
                            <tr class="ticket-row">
                                <td><strong>{{ $ticket->ticket_no }}</strong></td>
                                <td>{{ $ticket->restaurant->name ?? 'N/A' }}</div>
                                <td>{{ Str::limit($ticket->subject, 40) }}</div>
                                <td><span class="priority-badge priority-{{ $ticket->priority }}">{{ $ticket->priority }}</span></td>
                                <td><span class="badge status-{{ $ticket->status }}">{{ str_replace('_', ' ', $ticket->status) }}</span></td>
                                <td>{{ $ticket->created_at->format('d M Y') }}</div>
                                <td>
                                    <a href="{{ route('admin.support.ticket.view', $ticket->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                 </div>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No tickets found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $tickets->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

@include('includes.script')
</body>
</html>