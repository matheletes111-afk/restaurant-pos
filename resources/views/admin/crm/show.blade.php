<!DOCTYPE html>
<html lang="en">
<head>
    <title>Lead Details | Admin CRM</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Premium Styling for Lead Details page */
        .details-wrapper {
            display: grid;
            grid-template-columns: 320px 1fr 1fr;
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 1200px) {
            .details-wrapper {
                grid-template-columns: 1fr;
            }
        }

        .details-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            margin-bottom: 24px;
        }

        /* Left Column: Profile Card */
        .profile-card {
            text-align: center;
        }

        .avatar-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: white;
            font-size: 32px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px auto;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        }

        .profile-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .profile-role {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 24px;
        }

        .profile-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 12px;
            border: 1px solid #f1f5f9;
            font-size: 0.85rem;
            text-align: left;
        }

        .info-label {
            color: #64748b;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-value {
            font-weight: 600;
            color: #1e293b;
            word-break: break-all;
            text-align: right;
            max-width: 60%;
        }

        /* Middle Column: Log Notes & Timeline */
        .card-header-styled {
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }

        .card-header-styled i {
            color: #4f46e5;
            font-size: 1.2rem;
        }

        .card-header-styled h4 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .log-textarea {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px;
            font-size: 0.9rem;
            resize: none;
            transition: all 0.2s;
        }

        .log-textarea:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .timeline-section {
            margin-top: 30px;
            border-left: 2px solid #e2e8f0;
            padding-left: 20px;
            position: relative;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            padding: 15px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -27px;
            top: 20px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #4f46e5;
            border: 2px solid #ffffff;
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
        }

        .timeline-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 8px;
        }

        .timeline-user {
            font-weight: 600;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .timeline-user i {
            color: #818cf8;
        }

        .timeline-notes {
            font-size: 0.85rem;
            color: #334155;
            line-height: 1.45;
        }

        /* Right Column: Follow-up Tasks */
        .task-input-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }

        .task-input {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.85rem;
            height: 38px;
        }

        .task-input:focus {
            border-color: #6366f1;
            box-shadow: none;
        }

        .task-textarea {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 0.85rem;
            resize: none;
            margin-bottom: 16px;
        }

        .task-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 12px;
            transition: all 0.2s;
        }

        .task-checkbox {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
            margin-top: 2px;
            cursor: pointer;
        }

        .task-content {
            flex-grow: 1;
        }

        .task-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 2px;
        }

        .task-desc {
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 6px;
        }

        .task-meta {
            font-size: 0.7rem;
            color: #ea580c;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .task-item.completed {
            opacity: 0.6;
        }

        .task-item.completed .task-title {
            text-decoration: line-through;
            color: #94a3b8;
        }

        .task-item.completed .task-meta {
            color: #94a3b8;
        }

        .status-badge-styled {
            font-size: 0.7rem;
            text-transform: uppercase;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-block;
        }

        .sb-contacted { background: #f3e8ff; color: #7c3aed; }
        .sb-qualified { background: #fce7f3; color: #db2777; }
        .sb-nurturing { background: #ffedd5; color: #ea580c; }
        .sb-converted { background: #d1fae5; color: #047857; }
        .sb-lost { background: #fee2e2; color: #b91c1c; }

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
        .toast-success { background: #10b981; border-left: 5px solid #047857; }
        .toast-error { background: #ef4444; border-left: 5px solid #b91c1c; }
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
                                <h5 class="m-b-10">Lead Profile</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="">Console</a></li>
                                <li class="breadcrumb-item"><a href="">System</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.crm.index') }}">Admin CRM</a></li>
                                <li class="breadcrumb-item" aria-current="page">Lead Profile</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lead Details Wrapper -->
            <div class="details-wrapper">
                
                <!-- Left Column: Profile Card -->
                <div class="details-card profile-card">
                    <div class="avatar-circle">
                        {{ strtoupper(substr($lead->full_name, 0, 1)) }}
                    </div>
                    <h3 class="profile-name">{{ $lead->full_name }}</h3>
                    <div class="profile-role">Demo Booking Lead</div>

                    <div class="profile-info-row">
                        <div class="info-label"><i class="fa-regular fa-envelope"></i> Email</div>
                        <div class="info-value"><a href="mailto:{{ $lead->email_address }}">{{ $lead->email_address }}</a></div>
                    </div>

                    @if($lead->phone_number)
                    <div class="profile-info-row">
                        <div class="info-label"><i class="fa-solid fa-phone"></i> Phone</div>
                        <div class="info-value"><a href="tel:{{ $lead->phone_number }}">{{ $lead->phone_number }}</a></div>
                    </div>
                    @endif

                    <div class="profile-info-row">
                        <div class="info-label"><i class="fa-solid fa-store"></i> Restaurant</div>
                        <div class="info-value"><strong>{{ $lead->restaurant_name ?? 'N/A' }}</strong></div>
                    </div>

                    <div class="profile-info-row">
                        <div class="info-label"><i class="fa-solid fa-share-nodes"></i> Source</div>
                        <div class="info-value">{{ $lead->source ?? 'Direct Lead' }}</div>
                    </div>

                    <div class="profile-info-row">
                        <div class="info-label"><i class="fa-regular fa-calendar"></i> Registered</div>
                        <div class="info-value">{{ $lead->created_at->format('M d, Y') }}</div>
                    </div>

                    <div class="profile-info-row">
                        <div class="info-label"><i class="fa-solid fa-signal"></i> Pipeline Stage</div>
                        <div class="info-value">
                            @php
                                $sbClass = 'sb-contacted';
                                if ($lead->status === 'Qualified') $sbClass = 'sb-qualified';
                                elseif ($lead->status === 'Nurturing') $sbClass = 'sb-nurturing';
                                elseif ($lead->status === 'Converted') $sbClass = 'sb-converted';
                                elseif ($lead->status === 'Lost') $sbClass = 'sb-lost';
                            @endphp
                            <span class="status-badge-styled {{ $sbClass }}">{{ $lead->status }}</span>
                        </div>
                    </div>
                </div>

                <!-- Middle Column: Administrative Log Notes & Timeline -->
                <div class="details-card">
                    <div class="card-header-styled">
                        <i class="fa-regular fa-comment-dots"></i>
                        <h4>Administrative Log Notes</h4>
                    </div>

                    <!-- Log Form -->
                    <form id="logNoteForm" method="POST" action="{{ route('admin.crm.log-note', $lead->id) }}">
                        @csrf
                        <div class="mb-3">
                            <textarea class="form-control log-textarea" name="notes" rows="4" 
                                      placeholder="e.g. Spoke on Google Meet. Lead is looking to upgrade from manual billing. High interest lead." required></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-dark btn-sm">
                                <i class="fa-regular fa-clipboard me-2"></i> Log Interaction Note
                            </button>
                        </div>
                    </form>

                    <!-- Interaction Timeline -->
                    <div class="timeline-section">
                        @forelse($lead->interactions as $interaction)
                            <div class="timeline-item">
                                <div class="timeline-header">
                                    <div class="timeline-user">
                                        <i class="fa-solid fa-circle-user"></i>
                                        {{ $interaction->user->name ?? 'System' }}
                                    </div>
                                    <div>
                                        {{ $interaction->created_at->format('n/j/Y \a\t g:i A') }}
                                    </div>
                                </div>
                                <div class="timeline-notes">
                                    {{ $interaction->notes }}
                                </div>
                            </div>
                        @empty
                            <div style="color: #94a3b8; text-align: center; font-size: 0.85rem; padding: 20px 0;">
                                No interaction notes logged yet.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Right Column: Lead Follow-up Tasks -->
                <div class="details-card">
                    <div class="card-header-styled">
                        <i class="fa-regular fa-square-check"></i>
                        <h4>Lead Follow-up Tasks</h4>
                    </div>

                    <!-- Add Task Form -->
                    <form id="addTaskForm" method="POST" action="{{ route('admin.crm.add-task', $lead->id) }}">
                        @csrf
                        <div class="task-input-row">
                            <input type="text" name="task_title" class="form-control task-input" placeholder="Task Title (e.g. Schedule onboarding)" required>
                            <input type="date" name="due_date" class="form-control task-input">
                        </div>
                        <div class="mb-3">
                            <textarea name="description" class="form-control task-textarea" rows="2" placeholder="Description (optional)"></textarea>
                        </div>
                        <div class="text-end mb-4">
                            <button type="submit" class="btn btn-secondary btn-sm" style="background: #64748b; border: none;">
                                <i class="fa-solid fa-plus me-1"></i> Add Reminder Task
                            </button>
                        </div>
                    </form>

                    <!-- Tasks List -->
                    <div id="tasksContainer">
                        @forelse($lead->tasks as $task)
                            <div class="task-item {{ $task->is_completed ? 'completed' : '' }}" data-task-id="{{ $task->id }}">
                                <input type="checkbox" class="task-checkbox" {{ $task->is_completed ? 'checked' : '' }}>
                                <div class="task-content">
                                    <div class="task-title">{{ $task->task_title }}</div>
                                    @if($task->description)
                                        <div class="task-desc">{{ $task->description }}</div>
                                    @endif
                                    @if($task->due_date)
                                        <div class="task-meta">
                                            <i class="fa-regular fa-calendar-days"></i> Due: {{ $task->due_date->format('M d, Y') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-muted text-center py-4" id="emptyTasksMsg" style="font-size: 0.85rem;">
                                No follow-up tasks registered.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @include('includes.script')

    <script>
        $(document).ready(function() {
            // Display Toast Alerts
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

            // AJAX Log Note Form Submit
            $('#logNoteForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            showToast(response.message, 'success');
                            setTimeout(function() {
                                location.reload();
                            }, 800);
                        } else {
                            showToast(response.message || 'Error logging note.', 'error');
                        }
                    },
                    error: function(xhr) {
                        showToast('Failed to save log note. Please try again.', 'error');
                    }
                });
            });

            // AJAX Add Task Form Submit
            $('#addTaskForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            showToast(response.message, 'success');
                            setTimeout(function() {
                                location.reload();
                            }, 800);
                        } else {
                            showToast(response.message || 'Error adding task.', 'error');
                        }
                    },
                    error: function(xhr) {
                        showToast('Failed to add follow-up task.', 'error');
                    }
                });
            });

            // AJAX Toggle Task Completion Checklist
            $(document).on('change', '.task-checkbox', function() {
                const checkbox = $(this);
                const taskItem = checkbox.closest('.task-item');
                const taskId = taskItem.data('task-id');
                const url = "{{ url('admin/crm/tasks') }}/" + taskId + "/toggle";
                
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast(response.message, 'success');
                            if (response.is_completed) {
                                taskItem.addClass('completed');
                            } else {
                                taskItem.removeClass('completed');
                            }
                        } else {
                            showToast(response.message || 'Error updating task status.', 'error');
                            checkbox.prop('checked', !checkbox.prop('checked')); // revert
                        }
                    },
                    error: function(xhr) {
                        showToast('Failed to update task state.', 'error');
                        checkbox.prop('checked', !checkbox.prop('checked')); // revert
                    }
                });
            });
        });
    </script>
</body>
</html>
