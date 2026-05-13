<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Support Ticket</title>
    @include('includes.style')
</head>
<body>
@include('includes.sidebar')

<div class="pc-container">
    <div class="pc-content">
        <div class="page-header">
            <h5 class="m-b-10">Create Support Ticket</h5>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('restaurant.support.tickets') }}">My Tickets</a></li>
                <li class="breadcrumb-item active">Create Ticket</li>
            </ul>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>New Support Ticket</h5>
            </div>
            <div class="card-body">
                @include('includes.message')
                <form method="POST" action="{{ route('restaurant.support.store') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label>Subject <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control" required placeholder="Brief summary of your issue">
                    </div>
                    
                    <div class="form-group">
                        <label>Priority <span class="text-danger">*</span></label>
                        <select name="priority" class="form-control" required>
                            <option value="LOW">Low</option>
                            <option value="MEDIUM" selected>Medium</option>
                            <option value="HIGH">High</option>
                            <option value="URGENT">Urgent</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Message <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control" rows="6" required placeholder="Describe your issue in detail..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Submit Ticket
                    </button>
                    <a href="{{ route('restaurant.support.tickets') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

@include('includes.script')
</body>
</html>