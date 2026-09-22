@extends('layouts.app')

@section('title')
<title>Admin || Create Marketing Notification</title>
@endsection

@section('style')
@include('includes.style')
<style>
    .recipient-card {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 14px;
        transition: all 0.2s ease;
        background-color: #ffffff;
        cursor: pointer;
    }
    .recipient-card:hover {
        border-color: #ff6a00;
        background-color: #fffaf5;
    }
    .recipient-card.selected {
        border-color: #ff6a00;
        background-color: #fff6ee;
        box-shadow: 0 0 0 1px #ff6a00;
    }
    .recipient-list-box {
        max-height: 360px;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px;
        background-color: #f8fafc;
    }
    .merge-tag-badge {
        cursor: pointer;
        transition: transform 0.15s ease, background-color 0.15s ease;
        font-family: monospace;
        font-size: 12px;
        margin-right: 4px;
        margin-bottom: 6px;
        display: inline-block;
    }
    .merge-tag-badge:hover {
        transform: translateY(-1px);
        background-color: #ff6a00 !important;
        color: #ffffff !important;
    }
    .tox-tinymce {
        border-radius: 8px !important;
        border-color: #cbd5e1 !important;
    }
</style>
@endsection

@section('body')
@include('includes.sidebar')
<div class="pc-container">
  <div class="pc-content">

    <!-- Breadcrumb -->
    <div class="page-header">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
            <div class="page-header-title">
              <h5 class="m-b-10">Send Marketing Notification</h5>
            </div>
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('admin.marketing.index') }}">Marketing Notifications</a></li>
              <li class="breadcrumb-item" aria-current="page">Create Notification</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <!-- End Breadcrumb -->

    @include('includes.message')

    <form action="{{ route('admin.marketing.store') }}" method="POST" id="marketingForm">
      @csrf

      <div class="row">
        <!-- Left Column: Form Details -->
        <div class="col-lg-8">
          
          <!-- Card 1: Subject & Content -->
          <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
              <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-envelope-open-text text-primary me-2"></i> Notification Details
              </h5>
            </div>
            <div class="card-body p-4">
              
              <!-- Title / Subject -->
              <div class="mb-3">
                <label for="title" class="form-label fw-bold">
                  Email Subject / Notification Title <span class="text-danger">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       class="form-control form-control-lg @error('title') is-invalid @enderror" 
                       placeholder="e.g. Exciting New Features & Updates for Your Restaurant POS!" 
                       value="{{ old('title') }}" 
                       required>
                @error('title')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Merge Tags Helper -->
              <div class="mb-2 p-3 bg-light rounded border">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <span class="small fw-bold text-secondary">
                    <i class="fas fa-magic text-warning me-1"></i> Personalized Merge Tags (Click to insert into editor):
                  </span>
                  <span class="small text-muted">Automatically replaced per recipient</span>
                </div>
                <div>
                  <span class="badge bg-secondary merge-tag-badge" data-tag="{owner_name}">{owner_name}</span>
                  <span class="badge bg-secondary merge-tag-badge" data-tag="{restaurant_name}">{restaurant_name}</span>
                  <span class="badge bg-secondary merge-tag-badge" data-tag="{email}">{email}</span>
                  <span class="badge bg-secondary merge-tag-badge" data-tag="{restaurant_id}">{restaurant_id}</span>
                  <span class="badge bg-secondary merge-tag-badge" data-tag="{login_url}">{login_url}</span>
                  <span class="badge bg-secondary merge-tag-badge" data-tag="{year}">{year}</span>
                </div>
              </div>

              <!-- TinyMCE Description Body -->
              <div class="mb-3">
                <label for="description" class="form-label fw-bold">
                  Email Message Body (Free Rich Text Editor) <span class="text-danger">*</span>
                </label>
                <textarea name="description" 
                          id="description" 
                          class="form-control @error('description') is-invalid @enderror" 
                          rows="15">{{ old('description', '<h2>Special Announcement for {restaurant_name}</h2><p>We are delighted to bring you exciting new enhancements to boost your restaurant operations and customer experience.</p><ul><li><strong>Faster Billing & POS:</strong> Seamless split payments and high-speed order processing.</li><li><strong>Live Inventory Management:</strong> Keep real-time track of ingredients and stocks.</li><li><strong>Digital QR Ordering:</strong> Allow guests to scan and order instantly from tables.</li></ul><p>Login to your restaurant portal today to explore all new features!</p>') }}</textarea>
                @error('description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

            </div>
          </div>

          <!-- Card 2: Recipient Selection -->
          <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
              <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-users text-primary me-2"></i> Select Restaurant Recipients (Owner Emails)
              </h5>
              <span class="badge bg-light-primary text-primary fs-7" id="selectedCounterBadge">
                Total Available: {{ $restaurantsWithEmailCount }} Restaurants
              </span>
            </div>
            <div class="card-body p-4">

              <!-- Target Type Selector -->
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <div class="form-check p-3 border rounded h-100 bg-light-primary border-primary">
                    <input class="form-check-input" type="radio" name="target_type" id="target_all" value="all" {{ old('target_type', 'all') === 'all' ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold d-block cursor-pointer ms-2" for="target_all">
                      <i class="fas fa-globe-americas text-primary me-1"></i> Send to All Active Restaurants
                      <span class="d-block small text-muted fw-normal mt-1">
                        Broadcasts to all {{ $restaurantsWithEmailCount }} registered restaurant owners with valid email addresses.
                      </span>
                    </label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-check p-3 border rounded h-100 bg-white">
                    <input class="form-check-input" type="radio" name="target_type" id="target_selected" value="selected" {{ old('target_type') === 'selected' ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold d-block cursor-pointer ms-2" for="target_selected">
                      <i class="fas fa-tasks text-success me-1"></i> Select Specific Restaurants (100+ Selection)
                      <span class="d-block small text-muted fw-normal mt-1">
                        Pick individual restaurants or use the "Select All" toggle below.
                      </span>
                    </label>
                  </div>
                </div>
              </div>

              <!-- Specific Restaurants Picker Container -->
              <div id="specificRestaurantsContainer" style="display: {{ old('target_type') === 'selected' ? 'block' : 'none' }};">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 p-2 bg-light rounded">
                  <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnSelectAll">
                      <i class="fas fa-check-square me-1"></i> Select All
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btnDeselectAll">
                      <i class="far fa-square me-1"></i> Deselect All
                    </button>
                  </div>

                  <!-- Live Search -->
                  <div style="min-width: 240px;">
                    <input type="text" id="restaurantFilterInput" class="form-control form-control-sm" placeholder="Search restaurant name, code, email...">
                  </div>
                </div>

                <!-- Scrollable Checkbox List -->
                <div class="recipient-list-box" id="recipientListBox">
                  <div class="row g-2" id="recipientCardsContainer">
                    @foreach($restaurants as $rest)
                    @php
                      $ownerEmail = trim($rest->owner?->email ?? '');
                      $hasValidEmail = !empty($ownerEmail) && filter_var($ownerEmail, FILTER_VALIDATE_EMAIL);
                      $uniqueCode = $rest->restaurant_id_unique ?? ('REST-' . str_pad($rest->id, 3, '0', STR_PAD_LEFT));
                    @endphp
                    <div class="col-md-6 restaurant-item-col" 
                         data-name="{{ strtolower($rest->name) }}" 
                         data-code="{{ strtolower($uniqueCode) }}"
                         data-email="{{ strtolower($ownerEmail) }}"
                         data-owner="{{ strtolower($rest->owner?->name ?? '') }}">
                      <label class="recipient-card d-block mb-0 @if(!$hasValidEmail) opacity-50 @endif" for="rest_check_{{ $rest->id }}">
                        <div class="d-flex align-items-start">
                          <input class="form-check-input mt-1 me-2 restaurant-checkbox" 
                                 type="checkbox" 
                                 name="restaurant_ids[]" 
                                 value="{{ $rest->id }}" 
                                 id="rest_check_{{ $rest->id }}"
                                 @if(!$hasValidEmail) disabled @endif
                                 {{ is_array(old('restaurant_ids')) && in_array($rest->id, old('restaurant_ids')) ? 'checked' : '' }}>
                          <div class="w-100 overflow-hidden">
                            <div class="d-flex justify-content-between align-items-center">
                              <span class="fw-bold text-dark text-truncate d-inline-block" style="max-width: 170px;" title="{{ $rest->name }}">
                                {{ $rest->name }}
                              </span>
                              <span class="badge bg-light text-secondary border font-monospace fs-8">{{ $uniqueCode }}</span>
                            </div>
                            <div class="small text-muted text-truncate mt-1">
                              <i class="fas fa-user-circle me-1"></i> {{ $rest->owner?->name ?? 'No Owner' }}
                            </div>
                            <div class="small text-truncate mt-1 @if($hasValidEmail) text-primary @else text-danger @endif">
                              <i class="fas fa-envelope me-1"></i> {{ $ownerEmail ?: 'No Email Address' }}
                            </div>
                          </div>
                        </div>
                      </label>
                    </div>
                    @endforeach
                  </div>
                </div>
              </div>

            </div>
          </div>

        </div>

        <!-- Right Column: Settings, Schedule & Action -->
        <div class="col-lg-4">
          
          <!-- Card: Schedule & Timing -->
          <div class="card shadow-sm border-0 mb-4 sticky-top" style="top: 80px; z-index: 10;">
            <div class="card-header bg-white py-3 border-bottom">
              <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-clock text-primary me-2"></i> Notification Date & Actions
              </h5>
            </div>
            <div class="card-body p-4">
              
              <!-- Notification Date Input -->
              <div class="mb-3">
                <label for="notification_date" class="form-label fw-bold">
                  Notification Date & Time <span class="text-danger">*</span>
                </label>
                <input type="datetime-local" 
                       name="notification_date" 
                       id="notification_date" 
                       class="form-control form-control-lg @error('notification_date') is-invalid @enderror" 
                       value="{{ old('notification_date', now()->format('Y-m-d\TH:i')) }}" 
                       required>
                @error('notification_date')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="d-flex justify-content-between align-items-center mt-2">
                  <button type="button" class="btn btn-outline-secondary btn-sm" id="btnSetNow">
                    <i class="fas fa-bolt text-warning me-1"></i> Send Now (Immediate)
                  </button>
                  <small class="text-muted">Runs via Queue / Cron</small>
                </div>
              </div>

              <!-- Queue Scalability Note -->
              <div class="p-3 bg-light-primary rounded border border-primary border-opacity-25 mb-4">
                <div class="d-flex">
                  <i class="fas fa-server text-primary fs-4 me-2 mt-1"></i>
                  <div class="small">
                    <strong>100+ Restaurants Scalable</strong>
                    <div class="text-muted mt-1">
                      Emails are dispatched in the background through Laravel Queue jobs. It won't freeze your browser.
                    </div>
                  </div>
                </div>
              </div>

              <!-- Test Email Button -->
              <div class="mb-3">
                <button type="button" class="btn btn-outline-info w-100 mb-2" data-bs-toggle="modal" data-bs-target="#testEmailModal">
                  <i class="fas fa-paper-plane me-1"></i> Send Test Email Preview
                </button>
              </div>

              <hr class="my-3">

              <!-- Submit Button -->
              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg shadow" id="btnSubmitCampaign">
                  <i class="fas fa-paper-plane me-1"></i> Submit & Broadcast
                </button>
                <a href="{{ route('admin.marketing.index') }}" class="btn btn-light">
                  Cancel
                </a>
              </div>

            </div>
          </div>

        </div>
      </div>
    </form>

  </div>
</div>

<!-- Modal: Send Test Email -->
<div class="modal fade" id="testEmailModal" tabindex="-1" aria-labelledby="testEmailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light border-bottom">
        <h5 class="modal-title fw-bold" id="testEmailModalLabel">
          <i class="fas fa-vial text-info me-2"></i> Send Live Test Email Preview
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <p class="small text-muted mb-3">
          Send a sample test email to verify formatting, styles, and appearance before blasting to 100+ restaurant owners.
        </p>

        <div class="mb-3">
          <label for="test_email" class="form-label fw-bold">Send Test To (Email Address):</label>
          <input type="email" id="test_email" class="form-control" placeholder="your-email@example.com" value="{{ auth()->user()->email }}">
        </div>

        <div id="testEmailAlert" style="display: none;" class="alert mb-0"></div>
      </div>
      <div class="modal-footer bg-light border-top">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-info btn-sm text-white" id="btnSendTestEmail">
          <i class="fas fa-paper-plane me-1"></i> Send Test Now
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<!-- TinyMCE 6 Free Community CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Initialize TinyMCE Editor
    tinymce.init({
        selector: '#description',
        height: 420,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'fullscreen',
            'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table link | removeformat code fullscreen',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 15px; color: #334155; line-height: 1.6; }',
        branding: false,
        promotion: false,
    });

    // 2. Clickable Merge Tags Insert
    document.querySelectorAll('.merge-tag-badge').forEach(badge => {
        badge.addEventListener('click', function() {
            const tag = this.dataset.tag;
            tinymce.get('description').insertContent(tag);
        });
    });

    // 3. Set Now button
    document.getElementById('btnSetNow').addEventListener('click', function() {
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('notification_date').value = now.toISOString().slice(0, 16);
    });

    // 4. Target Type Toggle (All vs Selected)
    const targetAll = document.getElementById('target_all');
    const targetSelected = document.getElementById('target_selected');
    const specificContainer = document.getElementById('specificRestaurantsContainer');

    function toggleTargetView() {
        if (targetSelected.checked) {
            specificContainer.style.display = 'block';
            updateSelectedCounter();
        } else {
            specificContainer.style.display = 'none';
            document.getElementById('selectedCounterBadge').innerText = 'Target: All Active Restaurants';
        }
    }

    targetAll.addEventListener('change', toggleTargetView);
    targetSelected.addEventListener('change', toggleTargetView);

    // 5. Select / Deselect All
    const checkboxes = document.querySelectorAll('.restaurant-checkbox:not(:disabled)');
    
    document.getElementById('btnSelectAll').addEventListener('click', function() {
        checkboxes.forEach(cb => {
            const col = cb.closest('.restaurant-item-col');
            if (col.style.display !== 'none') {
                cb.checked = true;
                cb.closest('.recipient-card').classList.add('selected');
            }
        });
        updateSelectedCounter();
    });

    document.getElementById('btnDeselectAll').addEventListener('click', function() {
        checkboxes.forEach(cb => {
            const col = cb.closest('.restaurant-item-col');
            if (col.style.display !== 'none') {
                cb.checked = false;
                cb.closest('.recipient-card').classList.remove('selected');
            }
        });
        updateSelectedCounter();
    });

    // Card click & checkbox change handler
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            if (this.checked) {
                this.closest('.recipient-card').classList.add('selected');
            } else {
                this.closest('.recipient-card').classList.remove('selected');
            }
            updateSelectedCounter();
        });
    });

    function updateSelectedCounter() {
        if (targetSelected.checked) {
            const checkedCount = document.querySelectorAll('.restaurant-checkbox:checked').length;
            const totalEnabled = checkboxes.length;
            document.getElementById('selectedCounterBadge').innerText = `Selected: ${checkedCount} / ${totalEnabled} Restaurants`;
        }
    }

    // 6. Live Filter / Search Restaurants
    const filterInput = document.getElementById('restaurantFilterInput');
    filterInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        document.querySelectorAll('.restaurant-item-col').forEach(col => {
            const name = col.dataset.name || '';
            const code = col.dataset.code || '';
            const email = col.dataset.email || '';
            const owner = col.dataset.owner || '';

            if (name.includes(query) || code.includes(query) || email.includes(query) || owner.includes(query)) {
                col.style.display = '';
            } else {
                col.style.display = 'none';
            }
        });
    });

    // 7. Form Submission Guard
    const form = document.getElementById('marketingForm');
    form.addEventListener('submit', function(e) {
        // Force TinyMCE content sync
        tinymce.triggerSave();

        const title = document.getElementById('title').value.trim();
        const content = tinymce.get('description').getContent().trim();

        if (!title) {
            alert('Please enter a notification subject/title.');
            e.preventDefault();
            return;
        }

        if (!content) {
            alert('Please enter email message content.');
            e.preventDefault();
            return;
        }

        if (targetSelected.checked) {
            const checkedCount = document.querySelectorAll('.restaurant-checkbox:checked').length;
            if (checkedCount === 0) {
                alert('Please select at least one restaurant to send this notification.');
                e.preventDefault();
                return;
            }
        }

        const submitBtn = document.getElementById('btnSubmitCampaign');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Scheduling Broadcast...';
    });

    // 8. Send Test Email AJAX
    const btnSendTest = document.getElementById('btnSendTestEmail');
    btnSendTest.addEventListener('click', function() {
        tinymce.triggerSave();
        const testEmail = document.getElementById('test_email').value.trim();
        const title = document.getElementById('title').value.trim();
        const description = tinymce.get('description').getContent().trim();
        const alertEl = document.getElementById('testEmailAlert');

        if (!testEmail) {
            alertEl.className = 'alert alert-danger';
            alertEl.innerText = 'Please enter an email address for testing.';
            alertEl.style.display = 'block';
            return;
        }

        if (!title || !description) {
            alertEl.className = 'alert alert-danger';
            alertEl.innerText = 'Please provide both Title and Description before sending a test.';
            alertEl.style.display = 'block';
            return;
        }

        btnSendTest.disabled = true;
        btnSendTest.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';
        alertEl.style.display = 'none';

        fetch("{{ route('admin.marketing.send-test') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                test_email: testEmail,
                title: title,
                description: description
            })
        })
        .then(res => res.json())
        .then(data => {
            btnSendTest.disabled = false;
            btnSendTest.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Send Test Now';
            if (data.success) {
                alertEl.className = 'alert alert-success';
                alertEl.innerText = data.message;
            } else {
                alertEl.className = 'alert alert-danger';
                alertEl.innerText = data.message || 'Failed to send test email.';
            }
            alertEl.style.display = 'block';
        })
        .catch(err => {
            btnSendTest.disabled = false;
            btnSendTest.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Send Test Now';
            alertEl.className = 'alert alert-danger';
            alertEl.innerText = 'An error occurred while sending the test email.';
            alertEl.style.display = 'block';
        });
    });
});
</script>
@endsection
