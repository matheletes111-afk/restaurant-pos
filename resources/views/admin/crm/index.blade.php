<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin CRM Control Deck | Admin</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Custom Styles for Admin CRM Control Deck */
        .crm-header-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            border: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
        }
        
        .crm-header-icon {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            width: 54px;
            height: 54px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            margin-right: 20px;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }

        .crm-header-tag {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 700;
            color: #4f46e5;
            background: #f0f0ff;
            padding: 3px 8px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 6px;
        }

        .crm-header-title {
            font-size: 1.45rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        /* Filter Controls */
        .crm-controls-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            border: 1px solid #f1f5f9;
        }

        .search-wrapper {
            position: relative;
        }

        .search-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .search-input {
            padding-left: 45px;
            border-radius: 30px;
            border: 1px solid #e2e8f0;
            height: 45px;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .search-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .source-filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .filter-pill {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #64748b;
            padding: 8px 18px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-pill:hover {
            background: #f1f5f9;
            color: #334155;
            border-color: #cbd5e1;
        }

        .filter-pill.active {
            background: #4f46e5;
            color: #ffffff;
            border-color: #4f46e5;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.2);
        }

        /* Board Columns */
        .crm-board {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
            align-items: start;
        }
        @media (max-width: 1400px) {
            .crm-board {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (max-width: 992px) {
            .crm-board {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 768px) {
            .crm-board {
                grid-template-columns: 1fr;
            }
        }

        .crm-column {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 15px;
            min-height: 550px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .column-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .column-title {
            display: flex;
            align-items: center;
            font-size: 0.95rem;
            font-weight: 600;
            color: #334155;
            margin: 0;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 8px;
            display: inline-block;
        }

        .dot-contacted { background-color: #7c3aed; }
        .dot-qualified { background-color: #db2777; }
        .dot-nurturing { background-color: #ea580c; }
        .dot-converted { background-color: #10b981; }
        .dot-lost { background-color: #ef4444; }

        .column-count {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 600;
            width: 24px;
            height: 24px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .empty-stage-box {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 30px 15px;
            text-align: center;
            color: #94a3b8;
            font-size: 0.85rem;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 120px;
            background: #ffffff;
        }

        /* Lead Cards */
        .lead-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            transition: all 0.25s ease;
            position: relative;
        }

        .lead-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
            border-color: #cbd5e1;
        }

        .lead-name {
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 2px;
        }

        .lead-restaurant {
            font-size: 0.8rem;
            color: #64748b;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .lead-details {
            font-size: 0.8rem;
            color: #475569;
            margin-bottom: 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .lead-details a {
            color: #475569;
            text-decoration: none;
            transition: color 0.2s;
        }

        .lead-details a:hover {
            color: #4f46e5;
        }

        .lead-badge {
            font-size: 0.65rem;
            text-transform: uppercase;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
        }

        .badge-social { background: #fee2e2; color: #b91c1c; }
        .badge-search { background: #dbeafe; color: #1d4ed8; }
        .badge-friend { background: #d1fae5; color: #047857; }
        .badge-default { background: #f1f5f9; color: #475569; }

        /* Card Actions */
        .card-actions {
            border-top: 1px solid #f1f5f9;
            padding-top: 12px;
            margin-top: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-select {
            flex-grow: 1;
            font-size: 0.8rem;
            height: 34px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            padding: 0 10px;
            color: #334155;
            background-color: #f8fafc;
        }

        .status-select:focus {
            border-color: #6366f1;
            box-shadow: none;
        }

        .action-icon-btn {
            width: 34px;
            height: 34px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s;
            padding: 0;
        }

        .action-icon-btn:hover {
            background: #f1f5f9;
            color: #4f46e5;
            border-color: #cbd5e1;
        }

        .action-icon-btn.btn-arrow {
            background: #4f46e5;
            color: white;
            border-color: #4f46e5;
        }

        .action-icon-btn.btn-arrow:hover {
            background: #4338ca;
            color: white;
        }

        /* Followup details container */
        .followup-box {
            background: #fafafa;
            border: 1px solid #f0f0f0;
            border-radius: 8px;
            padding: 8px 10px;
            margin-bottom: 12px;
            font-size: 0.75rem;
            color: #475569;
        }

        .followup-header {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            color: #7c3aed;
            margin-bottom: 3px;
        }

        .followup-notes-text {
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        /* Toast notifications */
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #334155;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: none;
            z-index: 9999;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .toast-success {
            background: #10b981;
            border-left: 5px solid #047857;
        }

            background: #ef4444;
            border-left: 5px solid #b91c1c;
        }

        /* Drag and Drop Visual Styles */
        .draggable-card {
            cursor: grab;
            user-select: none;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        
        .draggable-card:active {
            cursor: grabbing;
        }

        .draggable-card.dragging {
            opacity: 0.45;
            transform: scale(0.96);
            border: 2px dashed #6366f1;
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.15);
        }

        .column-cards {
            min-height: 450px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            padding-bottom: 50px; /* spacing to easily drop cards at bottom */
            transition: background-color 0.2s ease, border-color 0.2s ease;
            border-radius: 8px;
            border: 2px dashed transparent;
        }

        .column-cards.drag-over {
            background-color: rgba(99, 102, 241, 0.04);
            border-color: #6366f1;
        }
    </style>
</head>
<body data-pc-theme="light">
    <!-- Loader -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

    <!-- Sidebar include -->
    @include('includes.sidebar')

    <!-- Toast container -->
    <div id="crm-toast" class="toast-notification"></div>

    <div class="pc-container">
        <div class="pc-content">
            <!-- Breadcrumbs -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Admin CRM</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="">Console</a></li>
                                <li class="breadcrumb-item"><a href="">System</a></li>
                                <li class="breadcrumb-item" aria-current="page">Admin CRM</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Header Card -->
            <div class="crm-header-card">
                <div class="crm-header-icon">
                    <i class="fa-solid fa-sparkles">✨</i>
                </div>
                <div>
                    <span class="crm-header-tag">OPERATIONS CONTROL + INTERACTIVE BOARD V2</span>
                    <h2 class="crm-header-title">Admin CRM Control Deck</h2>
                </div>
            </div>

            <!-- Control Card (Search & Source Filters) -->
            <div class="crm-controls-card">
                <form id="crmFilterForm" method="GET" action="{{ route('admin.crm.index') }}">
                    <input type="hidden" name="source" id="sourceFilterVal" value="{{ request('source', 'all') }}">
                    
                    <div class="row align-items-center g-3">
                        <div class="col-lg-5">
                            <div class="search-wrapper">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" name="search" class="form-control search-input" 
                                       placeholder="Search leads by name or email..." value="{{ request('search') }}">
                            </div>
                        </div>
                        
                        <div class="col-lg-7">
                            <div class="source-filters">
                                <div class="filter-pill {{ request('source', 'all') == 'all' ? 'active' : '' }}" data-source="all">
                                    All Sources
                                </div>
                                <div class="filter-pill {{ request('source') == 'Social Media' ? 'active' : '' }}" data-source="Social Media">
                                    Social Media
                                </div>
                                <div class="filter-pill {{ request('source') == 'Search Engine' ? 'active' : '' }}" data-source="Search Engine">
                                    Search Engine
                                </div>
                                <div class="filter-pill {{ request('source') == 'Friend/Colleague' ? 'active' : '' }}" data-source="Friend/Colleague">
                                    Friend/Colleague
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- CRM Board -->
            <div class="crm-board">
                
                <!-- Column: Contacted -->
                <div class="crm-column" data-status="Contacted">
                    <div class="column-header">
                        <h4 class="column-title">
                            <span class="status-dot dot-contacted"></span> Contacted
                        </h4>
                        <div class="column-count">
                            {{ count($leadsByStatus['Contacted']) }}
                        </div>
                    </div>
                    
                    <div class="column-cards" data-status="Contacted">
                        @forelse($leadsByStatus['Contacted'] as $lead)
                            @include('admin.crm._lead_card', ['lead' => $lead])
                        @empty
                            <div class="empty-stage-box">
                                No contacts in stage
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Column: Qualified -->
                <div class="crm-column" data-status="Qualified">
                    <div class="column-header">
                        <h4 class="column-title">
                            <span class="status-dot dot-qualified"></span> Qualified
                        </h4>
                        <div class="column-count">
                            {{ count($leadsByStatus['Qualified']) }}
                        </div>
                    </div>
                    
                    <div class="column-cards" data-status="Qualified">
                        @forelse($leadsByStatus['Qualified'] as $lead)
                            @include('admin.crm._lead_card', ['lead' => $lead])
                        @empty
                            <div class="empty-stage-box">
                                No contacts in stage
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Column: Nurturing -->
                <div class="crm-column" data-status="Nurturing">
                    <div class="column-header">
                        <h4 class="column-title">
                            <span class="status-dot dot-nurturing"></span> Nurturing
                        </h4>
                        <div class="column-count">
                            {{ count($leadsByStatus['Nurturing']) }}
                        </div>
                    </div>
                    
                    <div class="column-cards" data-status="Nurturing">
                        @forelse($leadsByStatus['Nurturing'] as $lead)
                            @include('admin.crm._lead_card', ['lead' => $lead])
                        @empty
                            <div class="empty-stage-box">
                                No contacts in stage
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Column: Converted -->
                <div class="crm-column" data-status="Converted">
                    <div class="column-header">
                        <h4 class="column-title">
                            <span class="status-dot dot-converted"></span> Converted
                        </h4>
                        <div class="column-count">
                            {{ count($leadsByStatus['Converted']) }}
                        </div>
                    </div>
                    
                    <div class="column-cards" data-status="Converted">
                        @forelse($leadsByStatus['Converted'] as $lead)
                            @include('admin.crm._lead_card', ['lead' => $lead])
                        @empty
                            <div class="empty-stage-box">
                                No contacts in stage
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Column: Lost -->
                <div class="crm-column" data-status="Lost">
                    <div class="column-header">
                        <h4 class="column-title">
                            <span class="status-dot dot-lost"></span> Lost
                        </h4>
                        <div class="column-count">
                            {{ count($leadsByStatus['Lost']) }}
                        </div>
                    </div>
                    
                    <div class="column-cards" data-status="Lost">
                        @forelse($leadsByStatus['Lost'] as $lead)
                            @include('admin.crm._lead_card', ['lead' => $lead])
                        @empty
                            <div class="empty-stage-box">
                                No contacts in stage
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Edit Followup Modal -->
    <div class="modal fade" id="followupModal" tabindex="-1" role="dialog" aria-labelledby="followupModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="followupForm" method="POST" action="">
                    @csrf
                    <div class="modal-header bg-light">
                        <h5 class="modal-title" id="followupModalLabel">
                            <i class="fa-regular fa-clock me-2"></i> Schedule Follow-up
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Follow-up Date & Time</label>
                            <input type="datetime-local" class="form-control" name="followup_date" id="modal_followup_date">
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Follow-up Notes</label>
                            <textarea class="form-control" name="followup_notes" id="modal_followup_notes" rows="4" placeholder="Enter notes or followup summary..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Follow-up</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @include('includes.script')

    <script>
        $(document).ready(function() {
            // --- DRAG AND DROP KANBAN IMPLEMENTATION ---
            let draggedCard = null;

            // Card Event Listeners
            $(document).on('dragstart', '.draggable-card', function(e) {
                draggedCard = this;
                $(this).addClass('dragging');
                
                // Store the lead ID in dataTransfer
                const leadId = $(this).data('lead-id');
                e.originalEvent.dataTransfer.setData('text/plain', leadId);
                e.originalEvent.dataTransfer.effectAllowed = 'move';
            });

            $(document).on('dragend', '.draggable-card', function(e) {
                $(this).removeClass('dragging');
                draggedCard = null;
                $('.column-cards').removeClass('drag-over');
            });

            // Column Event Listeners
            $('.column-cards').on('dragover', function(e) {
                e.preventDefault(); // Required to allow drop
                $(this).addClass('drag-over');
                e.originalEvent.dataTransfer.dropEffect = 'move';
            });

            $('.column-cards').on('dragleave', function(e) {
                $(this).removeClass('drag-over');
            });

            $('.column-cards').on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('drag-over');
                
                const targetColumnCards = $(this);
                const targetStatus = targetColumnCards.data('status');
                const leadId = e.originalEvent.dataTransfer.getData('text/plain');
                
                if (!leadId || !draggedCard) return;
                
                const cardElement = $(draggedCard);
                const sourceStatus = cardElement.data('status');
                
                // If dropping in same column, do nothing
                if (sourceStatus === targetStatus) return;

                // 1. Move card visually in the DOM
                // Remove empty placeholder in target column if any
                targetColumnCards.find('.empty-stage-box').remove();
                
                // Append card
                targetColumnCards.append(cardElement);
                
                // Update card status attribute and dropdown value
                cardElement.data('status', targetStatus);
                cardElement.find('.status-select').val(targetStatus);
                
                // 2. If source column is now empty, add a placeholder
                const sourceColumnCards = $(`.column-cards[data-status="${sourceStatus}"]`);
                if (sourceColumnCards.children('.lead-card').length === 0) {
                    sourceColumnCards.html('<div class="empty-stage-box">No contacts in stage</div>');
                }
                
                // 3. Update counter badges in headers
                updateColumnCounter(sourceStatus, -1);
                updateColumnCounter(targetStatus, 1);
                
                // 4. Send AJAX request to update database
                updateLeadStatusOnDb(leadId, targetStatus, cardElement, sourceStatus);
            });

            // Helper to update column counter badge
            function updateColumnCounter(status, offset) {
                const column = $(`.crm-column[data-status="${status}"]`);
                const countBadge = column.find('.column-count');
                let currentCount = parseInt(countBadge.text().trim()) || 0;
                countBadge.text(currentCount + offset);
            }

            // Helper to post status update to database
            function updateLeadStatusOnDb(leadId, newStatus, cardElement, originalStatus) {
                const url = "{{ url('admin/crm/update-status') }}/" + leadId;
                
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        status: newStatus
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast(response.message, 'success');
                        } else {
                            showToast(response.message || 'Error updating status.', 'error');
                            revertCardMovement(cardElement, originalStatus, newStatus);
                        }
                    },
                    error: function(xhr) {
                        showToast('Failed to update stage in database. Reverting...', 'error');
                        revertCardMovement(cardElement, originalStatus, newStatus);
                    }
                });
            }

            // Helper to revert card in case of AJAX failure
            function revertCardMovement(cardElement, originalStatus, currentStatus) {
                const sourceColumnCards = $(`.column-cards[data-status="${originalStatus}"]`);
                const targetColumnCards = $(`.column-cards[data-status="${currentStatus}"]`);
                
                // Move card back in DOM
                sourceColumnCards.find('.empty-stage-box').remove();
                sourceColumnCards.append(cardElement);
                cardElement.data('status', originalStatus);
                cardElement.find('.status-select').val(originalStatus);
                
                // Add placeholder to target if it is now empty
                if (targetColumnCards.children('.lead-card').length === 0) {
                    targetColumnCards.html('<div class="empty-stage-box">No contacts in stage</div>');
                }
                
                // Recalculate counters
                updateColumnCounter(currentStatus, -1);
                updateColumnCounter(originalStatus, 1);
            }

            // Source Filter Click Trigger
            $('.filter-pill').on('click', function() {
                $('.filter-pill').removeClass('active');
                $(this).addClass('active');
                
                $('#sourceFilterVal').val($(this).data('source'));
                $('#crmFilterForm').submit();
            });

            // Auto-submit search on Enter key or trigger filter changes
            $('.search-input').on('keypress', function(e) {
                if (e.which == 13) {
                    $('#crmFilterForm').submit();
                }
            });

            // Smoothly display toast alerts
            function showToast(message, type = 'success') {
                const toast = $('#crm-toast');
                toast.text(message);
                toast.removeClass('toast-success toast-error');
                
                if (type === 'success') {
                    toast.addClass('toast-success');
                } else {
                    toast.addClass('toast-error');
                }
                
                toast.fadeIn(300).delay(3000).fadeOut(300);
            }

            // AJAX Status Update trigger on Arrow button click next to select
            $('.btn-arrow').on('click', function() {
                const card = $(this).closest('.lead-card');
                const leadId = card.data('lead-id');
                const selectElement = card.find('.status-select');
                const newStatus = selectElement.val();
                
                if (!newStatus) {
                    showToast('Please select a valid stage.', 'error');
                    return;
                }

                const url = "{{ url('admin/crm/update-status') }}/" + leadId;
                
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        status: newStatus
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast(response.message, 'success');
                            // Reload after a small delay to animate/re-layout board cleanly
                            setTimeout(function() {
                                location.reload();
                            }, 800);
                        } else {
                            showToast(response.message || 'Error updating status.', 'error');
                        }
                    },
                    error: function(xhr) {
                        showToast('Failed to update stage. Please try again.', 'error');
                    }
                });
            });

            // Modal setup for Followups
            $('.edit-followup-btn').on('click', function() {
                const leadId = $(this).data('id');
                const name = $(this).data('name');
                const dateVal = $(this).data('date');
                const notesVal = $(this).data('notes');
                
                // Set form action dynamically
                const actionUrl = "{{ url('admin/crm/update-followup') }}/" + leadId;
                $('#followupForm').attr('action', actionUrl);
                
                // Prefill fields
                if (dateVal) {
                    // Convert dateVal to local ISO format: YYYY-MM-DDTHH:MM
                    const dateObj = new Date(dateVal);
                    // Adjust timezone offset
                    const offset = dateObj.getTimezoneOffset() * 60000;
                    const localISOTime = (new Date(dateObj.getTime() - offset)).toISOString().slice(0, 16);
                    $('#modal_followup_date').val(localISOTime);
                } else {
                    $('#modal_followup_date').val('');
                }
                
                $('#modal_followup_notes').val(notesVal || '');
                $('#followupModalLabel').html('<i class="fa-regular fa-clock me-2"></i> Follow-up for ' + name);
                
                // Show modal
                $('#followupModal').modal('show');
            });

            // Handle AJAX followup save to keep transitions smooth
            $('#followupForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const actionUrl = form.attr('action');
                
                $.ajax({
                    url: actionUrl,
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            $('#followupModal').modal('hide');
                            showToast(response.message, 'success');
                            setTimeout(function() {
                                location.reload();
                            }, 800);
                        } else {
                            showToast(response.message || 'Error saving followup.', 'error');
                        }
                    },
                    error: function(xhr) {
                        showToast('Failed to save followup. Please check fields.', 'error');
                    }
                });
            });
        });
    </script>
</body>
</html>
