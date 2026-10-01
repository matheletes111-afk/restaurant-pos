<!DOCTYPE html>
<html lang="en">
<head>
  <title>Ticket #{{ $ticket->ticket_no }} • Bill&Bite Support</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">

  @include('includes.style')

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Font Awesome 6 CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Support Tickets CSS -->
  <link rel="stylesheet" href="{{ asset('admin_template/css/support-tickets.css') }}">
</head>
<body data-pc-theme="light">

@include('includes.sidebar')

<div class="pc-container">
  <div class="pc-content">
    
    <div class="tkt-page-wrap">
      
      <!-- Top Banner -->
      <div class="pos-order-banner" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: var(--tkt-radius-lg); padding: 22px 26px; margin-bottom: 22px; color: #ffffff; position: relative; overflow: hidden;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
          <div class="d-flex align-items-center gap-3">
            <a href="{{ route('restaurant.support.tickets') }}" class="btn-back-square" title="Back to Tickets">
              <i class="fa-solid fa-arrow-left"></i>
            </a>

            <div>
              <span class="text-white-50" style="font-size: 0.76rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase;">
                Ticket #{{ $ticket->ticket_no }}
              </span>
              <h1 style="font-family: 'Outfit', sans-serif; font-size: 1.35rem; font-weight: 800; color: #ffffff; margin: 2px 0 0 0;">
                {{ $ticket->subject }}
              </h1>
            </div>
          </div>

          <div class="d-flex align-items-center gap-2 flex-wrap">
            @if($ticket->status == 'NEW')
              <span class="tkt-badge-status NEW"><i class="fa-solid fa-clock me-1"></i> Open</span>
            @elseif($ticket->status == 'IN_PROGRESS')
              <span class="tkt-badge-status IN_PROGRESS"><i class="fa-solid fa-spinner fa-spin me-1"></i> In Progress</span>
            @elseif($ticket->status == 'RESOLVED')
              <span class="tkt-badge-status RESOLVED"><i class="fa-solid fa-circle-check me-1"></i> Resolved</span>
            @endif

            <span class="tkt-badge-priority {{ $ticket->priority }}">
              {{ $ticket->priority }} Priority
            </span>
          </div>
        </div>
      </div>

      <!-- Flash Messages -->
      @include('includes.message')

      <div class="row">
        <!-- Main Conversation Area -->
        <div class="col-lg-8">
          
          <div class="tkt-form-card mb-4">
            <h3 style="font-family: 'Outfit', sans-serif; font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
              <i class="fa-solid fa-comments text-primary"></i>
              <span>Conversation Thread</span>
            </h3>

            <div class="tkt-chat-container">
              <!-- Initial Ticket Opening Message -->
              <div class="tkt-bubble-row from-restaurant">
                <div class="tkt-bubble">
                  <div class="tkt-bubble-author">
                    <i class="fa-solid fa-store"></i>
                    <span>You (Ticket Opener)</span>
                  </div>
                  <div class="tkt-bubble-content">
                    {{ $ticket->message }}
                  </div>
                  <div class="tkt-bubble-time">
                    <i class="fa-regular fa-clock"></i> {{ $ticket->created_at ? $ticket->created_at->format('d M Y, h:i A') : '' }}
                  </div>
                </div>
              </div>

              <!-- Conversation Replies -->
              @foreach($ticket->comments as $comment)
                @if($comment->user_type == 'ADMIN')
                  <!-- Support Agent Message (Left) -->
                  <div class="tkt-bubble-row from-admin">
                    <div class="tkt-bubble">
                      <div class="tkt-bubble-author">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Bill&amp;Bite Support Team</span>
                      </div>
                      <div class="tkt-bubble-content">
                        {{ $comment->comment }}
                      </div>
                      @if($comment->attachment)
                        <div class="mt-2 pt-2 border-top">
                          <a href="{{ asset($comment->attachment) }}" target="_blank" class="text-primary text-decoration-none fw-bold" style="font-size: 0.8rem;">
                            <i class="fa-solid fa-paperclip me-1"></i> View Attachment
                          </a>
                        </div>
                      @endif
                      <div class="tkt-bubble-time text-muted">
                        <i class="fa-regular fa-clock"></i> {{ $comment->created_at ? $comment->created_at->format('d M Y, h:i A') : '' }}
                      </div>
                    </div>
                  </div>
                @else
                  <!-- Restaurant Reply Message (Right) -->
                  <div class="tkt-bubble-row from-restaurant">
                    <div class="tkt-bubble">
                      <div class="tkt-bubble-author">
                        <i class="fa-solid fa-store"></i>
                        <span>You</span>
                      </div>
                      <div class="tkt-bubble-content">
                        {{ $comment->comment }}
                      </div>
                      @if($comment->attachment)
                        <div class="mt-2 pt-2 border-top">
                          <a href="{{ asset($comment->attachment) }}" target="_blank" class="text-white text-decoration-underline fw-bold" style="font-size: 0.8rem;">
                            <i class="fa-solid fa-paperclip me-1"></i> View Attachment
                          </a>
                        </div>
                      @endif
                      <div class="tkt-bubble-time">
                        <i class="fa-regular fa-clock"></i> {{ $comment->created_at ? $comment->created_at->format('d M Y, h:i A') : '' }}
                      </div>
                    </div>
                  </div>
                @endif
              @endforeach
            </div>

            <!-- Reply Form (if open) -->
            @if($ticket->status != 'RESOLVED')
            <div class="mt-4 pt-3 border-top">
              <form method="POST" action="{{ route('restaurant.support.comment.add', $ticket->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="tkt-form-group">
                  <label class="tkt-form-label">Add Reply / Update to Support</label>
                  <textarea name="comment" class="tkt-form-textarea" rows="4" required placeholder="Type your response or additional information here..."></textarea>
                </div>

                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                  <div class="form-group mb-0">
                    <label class="btn-tkt-secondary" style="margin-bottom: 0; cursor: pointer; padding: 0 16px; font-size: 0.8rem;">
                      <i class="fa-solid fa-paperclip"></i>
                      <span id="attachmentFileName">Attach File</span>
                      <input type="file" name="attachment" id="attachmentInput" style="display: none;" onchange="$('#attachmentFileName').text(this.files[0] ? this.files[0].name : 'Attach File');">
                    </label>
                  </div>

                  <button type="submit" class="btn-tkt-primary">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Send Reply</span>
                  </button>
                </div>
              </form>
            </div>
            @else
            <!-- Resolved Notice -->
            <div class="alert alert-success d-flex align-items-center gap-3 mt-4 mb-0" style="border-radius: 12px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857;">
              <i class="fa-solid fa-circle-check fa-xl"></i>
              <div>
                <strong>This ticket has been marked as resolved.</strong>
                <p class="mb-0" style="font-size: 0.82rem;">If you need assistance with a new question or issue, please create a new support ticket.</p>
              </div>
            </div>
            @endif

          </div>

        </div>

        <!-- Sidebar Info Card -->
        <div class="col-lg-4">
          
          <div class="p-4 bg-white rounded-3 border shadow-sm mb-4">
            <h5 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #0f172a; margin-bottom: 16px;">
              <i class="fa-solid fa-info-circle text-primary me-1"></i> Ticket Metadata
            </h5>

            <div class="d-flex flex-column gap-3" style="font-size: 0.85rem;">
              <div class="d-flex justify-content-between pb-2 border-bottom">
                <span class="text-muted">Ticket ID</span>
                <strong class="text-dark">{{ $ticket->ticket_no }}</strong>
              </div>

              <div class="d-flex justify-content-between pb-2 border-bottom">
                <span class="text-muted">Current Status</span>
                <div>{!! $ticket->statusBadge !!}</div>
              </div>

              <div class="d-flex justify-content-between pb-2 border-bottom">
                <span class="text-muted">Priority</span>
                <span class="tkt-badge-priority {{ $ticket->priority }}">{{ $ticket->priority }}</span>
              </div>

              <div class="d-flex justify-content-between pb-2 border-bottom">
                <span class="text-muted">Created Date</span>
                <span class="text-dark fw-bold">{{ $ticket->created_at ? $ticket->created_at->format('d M Y, h:i A') : '-' }}</span>
              </div>

              <div class="d-flex justify-content-between pb-2 border-bottom">
                <span class="text-muted">Last Updated</span>
                <span class="text-dark fw-bold">{{ $ticket->updated_at ? $ticket->updated_at->format('d M Y, h:i A') : '-' }}</span>
              </div>
            </div>

            @if($ticket->status != 'RESOLVED')
            <div class="mt-4 pt-2">
              <form method="POST" action="{{ route('restaurant.support.ticket.resolve', $ticket->id) }}">
                @csrf
                <button type="submit" class="btn btn-outline-success w-100 rounded-pill fw-bold" style="font-size: 0.85rem; padding: 10px;" onclick="return confirm('Are you sure you want to mark this ticket as resolved?')">
                  <i class="fa-solid fa-circle-check me-1"></i> Mark Ticket as Resolved
                </button>
              </form>
            </div>
            @endif
          </div>

        </div>
      </div>

    </div><!-- /.tkt-page-wrap -->

  </div><!-- /.pc-content -->
</div><!-- /.pc-container -->

<!-- JS & Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@include('includes.script')

</body>
</html>