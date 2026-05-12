<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - Enquiry Management</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #FF6B35;
            --primary-dark: #E85D2C;
            --success: #2E9E4F;
            --danger: #E76F51;
            --gray: #6C7A8A;
            --dark: #1A2C3E;
        }

        .stats-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
            border-bottom: 3px solid;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
        }
        .stats-label {
            color: var(--gray);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .enquiry-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s;
            border-left: 4px solid;
        }
        .enquiry-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .enquiry-status-new {
            border-left-color: #f59e0b;
            background: linear-gradient(135deg, #fff, #fffbeb);
        }
        .enquiry-status-at {
            border-left-color: #10b981;
        }
        .badge-new {
            background: #fef3c7;
            color: #92400e;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .badge-resolved {
            background: #d1fae5;
            color: #065f46;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .reply-section {
            background: #f0fdf4;
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
            border-left: 3px solid #10b981;
        }
        .filter-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .btn-reply {
            background: linear-gradient(135deg, var(--success), #219653);
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .btn-reply:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(46,158,79,0.3);
        }
        .enquiry-query {
            font-size: 0.95rem;
            color: var(--dark);
            line-height: 1.5;
        }
        .enquiry-meta {
            font-size: 0.75rem;
            color: var(--gray);
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        .select-all-checkbox {
            margin-right: 10px;
        }
        .bulk-actions {
            display: none;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .bulk-actions.show {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
        }
        .toast-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 9999;
            border-left: 4px solid;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .checkbox-col {
            width: 30px;
            text-align: center;
        }
    </style>
</head>

<body>
@include('includes.sidebar')

<div class="pc-container">
    <div class="pc-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Enquiry Management</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item active">Enquiries</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card" style="border-bottom-color: var(--primary);">
                    <div class="stats-number text-primary">{{ $statistics['total'] }}</div>
                    <div class="stats-label">Total Enquiries</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="border-bottom-color: #f59e0b;">
                    <div class="stats-number text-warning">{{ $statistics['new'] }}</div>
                    <div class="stats-label">New Enquiries</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="border-bottom-color: #10b981;">
                    <div class="stats-number text-success">{{ $statistics['resolved'] }}</div>
                    <div class="stats-label">Resolved</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="border-bottom-color: #8b5cf6;">
                    <div class="stats-number text-purple">{{ $statistics['this_week'] }}</div>
                    <div class="stats-label">This Week</div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-card">
            <form method="GET" action="{{ route('admin.enquiry.index') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status', 'all') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Restaurant</label>
                    <select name="restaurant_id" class="form-control">
                        <option value="all">All Restaurants</option>
                        @foreach($restaurants as $rest)
                            <option value="{{ $rest->id }}" {{ request('restaurant_id') == $rest->id ? 'selected' : '' }}>
                                {{ $rest->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.enquiry.export', request()->query()) }}" class="btn btn-success w-100">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Bulk Actions -->
        <div id="bulkActions" class="bulk-actions">
            <div>
                <strong id="selectedCount">0</strong> enquiries selected
            </div>
            <div>
                <button class="btn btn-sm btn-success" id="bulkResolveBtn">
                    <i class="fas fa-check-circle me-1"></i> Mark as Resolved
                </button>
                <button class="btn btn-sm btn-danger" id="bulkDeleteBtn">
                    <i class="fas fa-trash me-1"></i> Delete
                </button>
                <button class="btn btn-sm btn-secondary" id="clearSelectionBtn">Clear</button>
            </div>
        </div>

        <!-- Enquiries List -->
        @forelse($enquiries as $enquiry)
        <div class="enquiry-card enquiry-status-{{ $enquiry->status == 'NEW' ? 'new' : 'at' }}" data-id="{{ $enquiry->id }}">
            <div class="d-flex justify-content-between align-items-start flex-wrap">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-start gap-2">
                        <div class="checkbox-col">
                            <input type="checkbox" class="enquiry-checkbox" value="{{ $enquiry->id }}">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start flex-wrap">
                                <div>
                                    <h6 class="mb-1">
                                        <i class="fas fa-store text-muted me-1"></i>
                                        <strong>{{ $enquiry->restaurant->name ?? 'N/A' }}</strong>
                                        @if($enquiry->status == 'NEW')
                                            <span class="badge-new ms-2"><i class="fas fa-clock me-1"></i>New</span>
                                        @else
                                            <span class="badge-resolved ms-2"><i class="fas fa-check-circle me-1"></i>Resolved</span>
                                        @endif
                                    </h6>
                                </div>
                                <div class="enquiry-meta">
                                    <span><i class="fas fa-user"></i> By: {{ $enquiry->creator->name ?? 'Unknown' }}</span>
                                    <span><i class="fas fa-calendar"></i> {{ $enquiry->created_at->format('d M Y, h:i A') }}</span>
                                    @if($enquiry->query_reply)
                                    <span><i class="fas fa-reply-all"></i> Replied: {{ $enquiry->updated_at->format('d M Y') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="enquiry-query mt-2">
                                <strong>Query:</strong> {{ $enquiry->query }}
                            </div>
                            
                            @if($enquiry->query_reply)
                            <div class="reply-section">
                                <strong><i class="fas fa-reply text-success me-1"></i> Admin Response:</strong>
                                <p class="mt-2 mb-0">{{ $enquiry->query_reply }}</p>
                                <div class="enquiry-meta mt-2">
                                    <span><i class="fas fa-user-check"></i> Replied by: {{ $enquiry->replier->name ?? 'Admin' }}</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="btn-group ms-2">
                    @if($enquiry->status == 'NEW')
                        <button class="btn btn-sm btn-reply reply-btn" 
                                data-id="{{ $enquiry->id }}"
                                data-restaurant="{{ $enquiry->restaurant->name ?? 'N/A' }}"
                                data-query="{{ $enquiry->query }}">
                            <i class="fas fa-reply me-1"></i> Reply
                        </button>
                    @endif
                    <button class="btn btn-sm btn-danger delete-btn" 
                            data-id="{{ $enquiry->id }}"
                            data-restaurant="{{ $enquiry->restaurant->name ?? 'N/A' }}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <i class="fas fa-inbox" style="font-size: 48px; color: #cbd5e1;"></i>
            <h5 class="mt-3">No Enquiries Found</h5>
            <p class="text-muted">No enquiries match your filter criteria.</p>
        </div>
        @endforelse

        <!-- Pagination -->
        @if($enquiries->hasPages())
        <div class="mt-4 d-flex justify-content-end">
            {{ $enquiries->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-reply-all text-success me-2"></i>Reply to Enquiry</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="replyForm">
                @csrf
                <input type="hidden" name="enquiry_id" id="reply_enquiry_id">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-store me-2"></i>
                        <strong id="replyRestaurantName"></strong>
                    </div>
                    <div class="form-group">
                        <label><strong>Customer Query:</strong></label>
                        <div class="border rounded p-3 bg-light" id="originalQueryDisplay" style="max-height: 150px; overflow-y: auto;"></div>
                    </div>
                    <div class="form-group mt-3">
                        <label class="form-label required">Your Reply <span class="text-danger">*</span></label>
                        <textarea name="reply" id="replyText" class="form-control" rows="5" 
                                  placeholder="Type your response here..." required></textarea>
                        <small class="text-muted">Your reply will be sent to the restaurant owner.</small>
                        <div id="replyCharCount" class="text-muted mt-1" style="font-size: 0.7rem;">0/1000 characters</div>
                    </div>
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        After sending the reply, the enquiry status will be changed to <strong>"Action Taken"</strong>.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane me-2"></i>Send Reply
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle text-danger" style="font-size: 48px;"></i>
                </div>
                <p>Are you sure you want to delete this enquiry?</p>
                <p class="text-muted small" id="deleteRestaurantName"></p>
                <p class="text-danger small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@include('includes.script')

<script>
$(document).ready(function() {
    let deleteId = null;
    let selectedEnquiries = [];
    
    // Reply Button Click
    $('.reply-btn').on('click', function() {
        var id = $(this).data('id');
        var restaurant = $(this).data('restaurant');
        var query = $(this).data('query');
        
        $('#reply_enquiry_id').val(id);
        $('#replyRestaurantName').text('Restaurant: ' + restaurant);
        $('#originalQueryDisplay').text(query);
        $('#replyText').val('');
        $('#replyCharCount').text('0/1000 characters');
        $('#replyModal').modal('show');
    });
    
    // Character counter for reply
    $('#replyText').on('input', function() {
        var length = $(this).val().length;
        $('#replyCharCount').text(length + '/1000 characters');
        if (length > 1000) {
            $('#replyCharCount').css('color', 'red');
        } else {
            $('#replyCharCount').css('color', '#6c7a8a');
        }
    });
    
    // Submit Reply
    $('#replyForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#reply_enquiry_id').val();
        var reply = $('#replyText').val().trim();
        
        if (reply.length < 2) {
            showToast('Please enter a valid reply (minimum 2 characters)', 'error');
            return;
        }
        
        if (reply.length > 1000) {
            showToast('Reply cannot exceed 1000 characters', 'error');
            return;
        }
        
        var submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Sending...');
        
        $.ajax({
            url: "{{ route('admin.enquiry.reply', '') }}/" + id,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                reply: reply
            },
            success: function(response) {
                if (response.success) {
                    $('#replyModal').modal('hide');
                    $('#replyForm')[0].reset();
                    showToast(response.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(response.message, 'error');
                    submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i>Send Reply');
                }
            },
            error: function(xhr) {
                var errorMsg = xhr.responseJSON?.message || 'Error sending reply';
                showToast(errorMsg, 'error');
                submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i>Send Reply');
            }
        });
    });
    
    // Delete Button Click
    $('.delete-btn').on('click', function() {
        deleteId = $(this).data('id');
        var restaurant = $(this).data('restaurant');
        $('#deleteRestaurantName').text('Restaurant: ' + restaurant);
        $('#deleteModal').modal('show');
    });
    
    // Confirm Delete
    $('#confirmDeleteBtn').on('click', function() {
        if (deleteId) {
            var deleteBtn = $(this);
            deleteBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');
            
            $.ajax({
                url: "{{ route('admin.enquiry.delete', '') }}/" + deleteId,
                type: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        $('#deleteModal').modal('hide');
                        showToast(response.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast(response.message, 'error');
                        deleteBtn.prop('disabled', false).html('Delete');
                    }
                },
                error: function(xhr) {
                    var errorMsg = xhr.responseJSON?.message || 'Error deleting enquiry';
                    showToast(errorMsg, 'error');
                    deleteBtn.prop('disabled', false).html('Delete');
                }
            });
        }
    });
    
    // Bulk Actions - Checkbox selection
    $('.enquiry-checkbox').on('change', function() {
        var checked = $('.enquiry-checkbox:checked').map(function() {
            return $(this).val();
        }).get();
        
        selectedEnquiries = checked;
        
        if (selectedEnquiries.length > 0) {
            $('#bulkActions').addClass('show');
            $('#selectedCount').text(selectedEnquiries.length);
        } else {
            $('#bulkActions').removeClass('show');
        }
    });
    
    // Select All functionality
    $('#selectAllCheckbox').on('change', function() {
        $('.enquiry-checkbox').prop('checked', $(this).is(':checked')).trigger('change');
    });
    
    // Bulk Resolve
    $('#bulkResolveBtn').on('click', function() {
        if (selectedEnquiries.length === 0) {
            showToast('Please select at least one enquiry', 'error');
            return;
        }
        
        if (confirm('Mark ' + selectedEnquiries.length + ' enquiries as resolved?')) {
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Processing...');
            
            $.ajax({
                url: "{{ route('admin.enquiry.bulk.action') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: selectedEnquiries,
                    action: 'mark_as_resolved'
                },
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast(response.message, 'error');
                        btn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> Mark as Resolved');
                    }
                },
                error: function(xhr) {
                    showToast('Error processing request', 'error');
                    btn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> Mark as Resolved');
                }
            });
        }
    });
    
    // Bulk Delete
    $('#bulkDeleteBtn').on('click', function() {
        if (selectedEnquiries.length === 0) {
            showToast('Please select at least one enquiry', 'error');
            return;
        }
        
        if (confirm('Delete ' + selectedEnquiries.length + ' enquiries? This action cannot be undone.')) {
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Deleting...');
            
            $.ajax({
                url: "{{ route('admin.enquiry.bulk.action') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: selectedEnquiries,
                    action: 'delete'
                },
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast(response.message, 'error');
                        btn.prop('disabled', false).html('<i class="fas fa-trash me-1"></i> Delete');
                    }
                },
                error: function(xhr) {
                    showToast('Error processing request', 'error');
                    btn.prop('disabled', false).html('<i class="fas fa-trash me-1"></i> Delete');
                }
            });
        }
    });
    
    // Clear Selection
    $('#clearSelectionBtn').on('click', function() {
        $('.enquiry-checkbox').prop('checked', false).trigger('change');
    });
    
    // Toast notification function
    function showToast(message, type = 'success') {
        var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        var color = type === 'success' ? '#10b981' : '#ef4444';
        
        var toastHtml = `
            <div class="toast-notification" style="border-left-color: ${color};">
                <i class="fas ${icon}" style="color: ${color}; margin-right: 10px;"></i>
                <span>${message}</span>
            </div>
        `;
        $('body').append(toastHtml);
        setTimeout(function() {
            $('.toast-notification').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 3000);
    }
});
</script>

<style>
    .text-purple {
        color: #8b5cf6;
    }
    .btn-group .btn {
        margin: 0 2px;
    }
    .modal-header {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-bottom: 1px solid #dee2e6;
    }
    .form-label.required::after {
        content: '*';
        color: #ef4444;
        margin-left: 4px;
    }
</style>

</body>
</html>