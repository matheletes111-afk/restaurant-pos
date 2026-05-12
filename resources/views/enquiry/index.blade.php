<!DOCTYPE html>
<html lang="en">
<head>
    <title>Enquiry Management - {{ auth()->user()->restaurant->name ?? 'Restaurant' }}</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
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
        }
        .enquiry-status-at {
            border-left-color: #10b981;
        }
        .enquiry-query {
            font-size: 0.95rem;
            color: #1e293b;
            margin-bottom: 10px;
        }
        .enquiry-meta {
            font-size: 0.75rem;
            color: #64748b;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .enquiry-reply {
            background: #f0fdf4;
            padding: 12px;
            border-radius: 8px;
            margin-top: 12px;
            border-left: 3px solid #10b981;
        }
        .reply-text {
            font-size: 0.85rem;
            color: #334155;
        }
        .badge-new {
            background: #fef3c7;
            color: #92400e;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .badge-action-taken {
            background: #d1fae5;
            color: #065f46;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .filter-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .page-header-custom {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border-radius: 20px;
            padding: 24px 30px;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
        }
        .page-header-custom h3 {
            color: white;
            font-weight: 700;
            margin-bottom: 5px;
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
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
        }
        .empty-state i {
            font-size: 48px;
            color: #cbd5e1;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
@include('includes.sidebar')

<div class="pc-container">
    <div class="pc-content">
        <!-- Page Header -->
        <div class="page-header-custom">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h3><i class="fas fa-question-circle me-2"></i>Enquiry Management</h3>
                    <p class="text-white-50 mb-0">Submit and track your queries</p>
                </div>
                <div>
                    <button class="btn btn-light" data-toggle="modal" data-target="#addEnquiryModal">
                        <i class="fas fa-plus me-2"></i>New Enquiry
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-card">
            <form method="GET" action="{{ route('enquiry.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
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
                    <label class="form-label">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Enquiries List -->
        @forelse($enquiries as $enquiry)
        <div class="enquiry-card enquiry-status-{{ $enquiry->status == 'NEW' ? 'new' : 'at' }}">
            <div class="d-flex justify-content-between align-items-start flex-wrap">
                <div class="flex-grow-1">
                    <div class="enquiry-query">
                        <strong>Query:</strong> {{ $enquiry->query }}
                    </div>
                    <div class="enquiry-meta">
                        <span><i class="fas fa-calendar-alt"></i> {{ $enquiry->created_at->format('d M Y, h:i A') }}</span>
                        <span>
                            @if($enquiry->status == 'NEW')
                                <span class="badge-new"><i class="fas fa-clock"></i> New</span>
                            @else
                                <span class="badge-action-taken"><i class="fas fa-check-circle"></i> Action Taken</span>
                            @endif
                        </span>
                        @if($enquiry->replier_by)
                        <span><i class="fas fa-user-check"></i> Replied by: {{ $enquiry->replier->name ?? 'Admin' }}</span>
                        @endif
                    </div>
                    
                    @if($enquiry->query_reply)
                    <div class="enquiry-reply">
                        <div class="reply-text">
                            <strong><i class="fas fa-reply-all"></i> Response:</strong> {{ $enquiry->query_reply }}
                        </div>
                        <div class="enquiry-meta mt-2">
                            <span><i class="fas fa-calendar"></i> {{ $enquiry->updated_at->format('d M Y, h:i A') }}</span>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="btn-group ml-2">
                    @if($enquiry->status == 'NEW')
                        <button class="btn btn-sm btn-info view-btn" 
                                data-id="{{ $enquiry->id }}"
                                data-query="{{ $enquiry->query }}"
                                data-created="{{ $enquiry->created_at->format('d M Y, h:i A') }}">
                            <i class="fas fa-eye"></i> View
                        </button>
                    @endif
                    <button class="btn btn-sm btn-danger delete-btn" 
                            data-id="{{ $enquiry->id }}"
                            data-query="{{ Str::limit($enquiry->query, 50) }}">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h5>No Enquiries Found</h5>
            <p class="text-muted">Click "New Enquiry" to submit your first query.</p>
            <button class="btn btn-primary mt-3" data-toggle="modal" data-target="#addEnquiryModal">
                <i class="fas fa-plus me-2"></i>Submit New Enquiry
            </button>
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

<!-- Add Enquiry Modal -->
<div class="modal fade" id="addEnquiryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle text-primary me-2"></i>Submit New Enquiry</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="addEnquiryForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Your Query <span class="text-danger">*</span></label>
                        <textarea name="query" id="enquiryQuery" class="form-control" rows="6" 
                                  placeholder="Please describe your query or issue in detail..." required></textarea>
                      
                    </div>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Your enquiry will be reviewed by our support team. You will receive a response within 24 hours.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Submit Enquiry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Enquiry Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye text-info me-2"></i>Enquiry Details</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label><strong>Submitted On:</strong></label>
                    <p id="viewCreatedAt" class="text-muted"></p>
                </div>
                <div class="form-group">
                    <label><strong>Your Query:</strong></label>
                    <p id="viewQuery" class="border rounded p-3 bg-light"></p>
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-clock me-2"></i>
                    <strong>Status: Pending</strong><br>
                    Your enquiry is waiting for response from our support team.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
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
                <p class="text-muted small" id="deleteQueryPreview"></p>
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
    // Character counter for enquiry
    $('#enquiryQuery').on('input', function() {
        var length = $(this).val().length;
        $('#charCount').text(length +  'characters');
        
    });
    
    // Add Enquiry
    $('#addEnquiryForm').on('submit', function(e) {
        e.preventDefault();
        var query = $('#enquiryQuery').val().trim();
        
        if (query.length < 3) {
            showToast('Please enter at least 3 characters', 'error');
            return;
        }
        
        
        
        var formData = $(this).serialize();
        var submitBtn = $(this).find('button[type="submit"]');
        
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Submitting...');
        
        $.ajax({
            url: "{{ route('enquiry.store') }}",
            type: "POST",
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#addEnquiryModal').modal('hide');
                    $('#addEnquiryForm')[0].reset();
                    
                    showToast(response.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(response.message, 'error');
                    submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i>Submit Enquiry');
                }
            },
            error: function(xhr) {
                var errorMsg = xhr.responseJSON?.message || 'Error submitting enquiry';
                showToast(errorMsg, 'error');
                submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i>Submit Enquiry');
            }
        });
    });
    
    // View Button Click
    $('.view-btn').on('click', function() {
        var query = $(this).data('query');
        var createdAt = $(this).data('created');
        
        $('#viewQuery').text(query);
        $('#viewCreatedAt').text(createdAt);
        $('#viewModal').modal('show');
    });
    
    // Delete Button Click
    var deleteId = null;
    $('.delete-btn').on('click', function() {
        deleteId = $(this).data('id');
        var query = $(this).data('query');
        $('#deleteQueryPreview').text(query);
        $('#deleteModal').modal('show');
    });
    
    // Confirm Delete
    $('#confirmDeleteBtn').on('click', function() {
        if (deleteId) {
            var deleteBtn = $(this);
            deleteBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');
            
            $.ajax({
                url: "{{ route('enquiry.delete', '') }}/" + deleteId,
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
    .badge-new, .badge-action-taken {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .btn-group .btn {
        margin: 0 2px;
    }
    .modal-header {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-bottom: 1px solid #dee2e6;
    }
</style>

</body>
</html>