<!DOCTYPE html>
<html lang="en">
<head>
  <title>Customer Support &amp; Help Desk • Bill&Bite POS</title>
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
      
      <!-- ===================================================
           1. TOP HEADER DECK
           =================================================== -->
      <div class="tkt-header-deck">
        <div class="tkt-header-left">
          <div class="tkt-header-icon">
            <i class="fa-solid fa-headset"></i>
          </div>
          <div class="tkt-header-title-meta">
            <span class="tkt-header-eyebrow">Help Desk &amp; Technical Assistance</span>
            <h1 class="tkt-header-title">Restaurant Support Tickets</h1>
            <p class="tkt-header-sub">Submit technical inquiries, report issues &amp; communicate directly with our dedicated POS support engineers</p>
          </div>
        </div>

        <div class="d-flex align-items-center gap-2">
          <a href="{{ route('restaurant.support.create') }}" class="btn-tkt-primary">
            <i class="fa-solid fa-circle-plus"></i>
            <span>Open New Ticket</span>
          </a>
        </div>
      </div>

      <!-- Flash Messages -->
      @include('includes.message')

      @php
        $allTickets = $tickets->items();
        $totalCount = $tickets->total();
        $newCount = collect($allTickets)->where('status', 'NEW')->count();
        $progressCount = collect($allTickets)->where('status', 'IN_PROGRESS')->count();
        $resolvedCount = collect($allTickets)->where('status', 'RESOLVED')->count();
      @endphp

      <!-- ===================================================
           2. STATS OVERVIEW DECK
           =================================================== -->
      <div class="tkt-stats-grid">
        <!-- Total Tickets -->
        <div class="tkt-stat-card stat-all">
          <div class="tkt-stat-info">
            <span class="tkt-stat-label">Total Tickets</span>
            <span class="tkt-stat-val">{{ $totalCount }}</span>
          </div>
          <div class="tkt-stat-icon">
            <i class="fa-solid fa-ticket"></i>
          </div>
        </div>

        <!-- Open / New -->
        <div class="tkt-stat-card stat-new">
          <div class="tkt-stat-info">
            <span class="tkt-stat-label">⏳ Open / New</span>
            <span class="tkt-stat-val">{{ $newCount }}</span>
          </div>
          <div class="tkt-stat-icon">
            <i class="fa-solid fa-clock"></i>
          </div>
        </div>

        <!-- In Progress -->
        <div class="tkt-stat-card stat-progress">
          <div class="tkt-stat-info">
            <span class="tkt-stat-label">👨‍💻 In Progress</span>
            <span class="tkt-stat-val">{{ $progressCount }}</span>
          </div>
          <div class="tkt-stat-icon">
            <i class="fa-solid fa-spinner"></i>
          </div>
        </div>

        <!-- Resolved -->
        <div class="tkt-stat-card stat-resolved">
          <div class="tkt-stat-info">
            <span class="tkt-stat-label">✅ Resolved</span>
            <span class="tkt-stat-val">{{ $resolvedCount }}</span>
          </div>
          <div class="tkt-stat-icon">
            <i class="fa-solid fa-circle-check"></i>
          </div>
        </div>
      </div>

      <!-- ===================================================
           3. TICKET LIST & CARDS
           =================================================== -->
      <div class="tkt-ticket-list">
        @forelse($tickets as $ticket)
        <a href="{{ route('restaurant.support.ticket.view', $ticket->id) }}" class="tkt-ticket-card status-{{ $ticket->status }}">
          <div class="tkt-ticket-top">
            <div>
              <span class="tkt-ticket-id-tag">
                <i class="fa-solid fa-hashtag"></i> {{ $ticket->ticket_no }}
              </span>
              <h3 class="tkt-ticket-title">{{ $ticket->subject }}</h3>
            </div>

            <div class="tkt-badge-cluster">
              @if($ticket->status == 'NEW')
                <span class="tkt-badge-status NEW"><i class="fa-solid fa-clock me-1"></i> Open</span>
              @elseif($ticket->status == 'IN_PROGRESS')
                <span class="tkt-badge-status IN_PROGRESS"><i class="fa-solid fa-spinner fa-spin me-1"></i> In Progress</span>
              @elseif($ticket->status == 'RESOLVED')
                <span class="tkt-badge-status RESOLVED"><i class="fa-solid fa-circle-check me-1"></i> Resolved</span>
              @else
                <span class="tkt-badge-status">{{ $ticket->status }}</span>
              @endif

              <span class="tkt-badge-priority {{ $ticket->priority }}">
                {{ $ticket->priority }} Priority
              </span>
            </div>
          </div>

          <p class="tkt-ticket-body-preview">
            {{ Str::limit($ticket->message, 140) }}
          </p>

          <div class="tkt-ticket-meta-row">
            <div class="d-flex align-items-center gap-3">
              <span class="tkt-meta-item">
                <i class="fa-regular fa-calendar-days"></i> {{ $ticket->created_at ? $ticket->created_at->format('d M Y, h:i A') : '-' }}
              </span>

              <span class="tkt-replies-pill">
                <i class="fa-regular fa-comments text-primary"></i>
                <span>{{ $ticket->comments->count() }} {{ $ticket->comments->count() == 1 ? 'Reply' : 'Replies' }}</span>
              </span>
            </div>

            <div style="font-weight: 700; color: var(--tkt-primary); display: flex; align-items: center; gap: 6px;">
              <span>View Conversation</span>
              <i class="fa-solid fa-arrow-right"></i>
            </div>
          </div>
        </a>
        @empty
        <!-- Empty State -->
        <div class="text-center py-5 px-3 bg-white rounded-3 shadow-sm border">
          <div style="width: 70px; height: 70px; border-radius: 50%; background: #fff0e6; color: #ff5e14; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin: 0 auto 16px;">
            <i class="fa-solid fa-ticket-simple"></i>
          </div>
          <h3 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #0f172a; margin-bottom: 6px;">No Support Tickets Found</h3>
          <p style="color: #64748b; font-size: 0.88rem; max-width: 420px; margin: 0 auto 20px;">Need technical help with your POS hardware, billing, printer, or online menu? Submit your first support inquiry.</p>
          <a href="{{ route('restaurant.support.create') }}" class="btn-tkt-primary">
            <i class="fa-solid fa-circle-plus"></i>
            <span>Create Support Ticket</span>
          </a>
        </div>
        @endforelse
      </div>

      <!-- Pagination -->
      <div class="d-flex justify-content-center mt-3">
        {{ $tickets->links() }}
      </div>

    </div><!-- /.tkt-page-wrap -->

  </div><!-- /.pc-content -->
</div><!-- /.pc-container -->

<!-- JS & Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@include('includes.script')

</body>
</html>