<!DOCTYPE html>
<html lang="en">
<head>
  <title>Open Support Ticket • Bill&Bite POS</title>
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
      
      <!-- Top Header Deck -->
      <div class="tkt-header-deck">
        <div class="tkt-header-left">
          <div class="tkt-header-icon">
            <i class="fa-solid fa-file-pen"></i>
          </div>
          <div class="tkt-header-title-meta">
            <span class="tkt-header-eyebrow">Technical Support Helpdesk</span>
            <h1 class="tkt-header-title">Create Support Ticket</h1>
            <p class="tkt-header-sub">Submit detailed information about the issue you are experiencing</p>
          </div>
        </div>

        <div>
          <a href="{{ route('restaurant.support.tickets') }}" class="btn-tkt-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Tickets</span>
          </a>
        </div>
      </div>

      <!-- Flash Messages -->
      @include('includes.message')

      <div class="row">
        <div class="col-lg-8">
          <div class="tkt-form-card">
            <form method="POST" action="{{ route('restaurant.support.store') }}">
              @csrf
              
              <div class="tkt-form-group">
                <label class="tkt-form-label">
                  Ticket Subject / Issue Title <span class="text-danger">*</span>
                </label>
                <input type="text" name="subject" class="tkt-form-input" required placeholder="e.g. KOT printer thermal timeout, QR menu sync issue..." value="{{ old('subject') }}" autocomplete="off">
                <small class="text-muted" style="font-size: 0.76rem;">Summarize your inquiry or problem in a single sentence.</small>
              </div>

              <div class="tkt-form-group">
                <label class="tkt-form-label">
                  Urgency &amp; Priority Level <span class="text-danger">*</span>
                </label>
                <select name="priority" class="tkt-form-select" required>
                  <option value="LOW" {{ old('priority') == 'LOW' ? 'selected' : '' }}>🟢 Low - General question or feedback</option>
                  <option value="MEDIUM" {{ old('priority', 'MEDIUM') == 'MEDIUM' ? 'selected' : '' }}>🔵 Medium - Minor issue, normal operations continue</option>
                  <option value="HIGH" {{ old('priority') == 'HIGH' ? 'selected' : '' }}>🟠 High - Feature failure affecting billing/KOT</option>
                  <option value="URGENT" {{ old('priority') == 'URGENT' ? 'selected' : '' }}>🔴 Urgent - Complete POS outage / critical blocking bug</option>
                </select>
              </div>

              <div class="tkt-form-group">
                <label class="tkt-form-label">
                  Detailed Issue Description <span class="text-danger">*</span>
                </label>
                <textarea name="message" class="tkt-form-textarea" rows="7" required placeholder="Please describe what happened, steps to reproduce, and any error messages shown on screen...">{{ old('message') }}</textarea>
                <small class="text-muted" style="font-size: 0.76rem;">Minimum 10 characters.</small>
              </div>

              <div class="d-flex align-items-center gap-3 mt-4 pt-2">
                <button type="submit" class="btn-tkt-primary">
                  <i class="fa-solid fa-paper-plane"></i>
                  <span>Submit Support Ticket</span>
                </button>
                <a href="{{ route('restaurant.support.tickets') }}" class="btn-tkt-secondary">Cancel</a>
              </div>
            </form>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="p-4 bg-white rounded-3 border shadow-sm mb-4">
            <h5 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #0f172a; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
              <i class="fa-solid fa-circle-info text-primary"></i>
              <span>Tips for Quick Resolution</span>
            </h5>
            <ul class="text-muted ps-3 mb-0" style="font-size: 0.84rem; line-height: 1.7;">
              <li><strong>Be specific:</strong> Include table numbers, item names, or order IDs if applicable.</li>
              <li><strong>Error details:</strong> Describe exact wording of any popups or warnings.</li>
              <li><strong>Live response:</strong> Our engineering team is automatically notified upon ticket submission.</li>
            </ul>
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