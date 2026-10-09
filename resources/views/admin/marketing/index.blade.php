@extends('layouts.app')

@section('title')
<title>Admin || Marketing Notifications</title>
@endsection

@section('style')
@include('includes.style')
<style>
    .stat-card {
        border-radius: 12px;
        border: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .campaign-title-link {
        color: #1e293b;
        font-weight: 600;
        text-decoration: none;
    }
    .campaign-title-link:hover {
        color: #ff6a00;
        text-decoration: underline;
    }
    .progress-compact {
        height: 7px;
        border-radius: 4px;
        background-color: #e2e8f0;
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
          <div class="col-md-8">
            <div class="page-header-title">
              <h5 class="m-b-10">Marketing Email Notifications</h5>
            </div>
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
              <li class="breadcrumb-item" aria-current="page">Marketing Notifications</li>
            </ul>
          </div>
          <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <a href="{{ route('admin.marketing.create') }}" class="btn btn-primary shadow-sm">
              <i class="fas fa-paper-plane me-1"></i> + Send Notification
            </a>
          </div>
        </div>
      </div>
    </div>
    <!-- End Breadcrumb -->

    @include('includes.message')

    <!-- Stats Overview Cards -->
    <div class="row mb-3 g-3">
      <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm border-0">
          <div class="card-body p-3">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-light-primary text-primary me-3">
                <i class="fas fa-bullhorn"></i>
              </div>
              <div>
                <span class="text-muted text-uppercase fs-7 fw-semibold">Total Campaigns</span>
                <h4 class="mb-0 fw-bold">{{ number_format($stats['total_campaigns']) }}</h4>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm border-0">
          <div class="card-body p-3">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-light-success text-success me-3">
                <i class="fas fa-check-circle"></i>
              </div>
              <div>
                <span class="text-muted text-uppercase fs-7 fw-semibold">Emails Delivered</span>
                <h4 class="mb-0 fw-bold text-success">{{ number_format($stats['total_sent']) }}</h4>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm border-0">
          <div class="card-body p-3">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-light-info text-info me-3">
                <i class="fas fa-calendar-alt"></i>
              </div>
              <div>
                <span class="text-muted text-uppercase fs-7 fw-semibold">Scheduled</span>
                <h4 class="mb-0 fw-bold text-info">{{ number_format($stats['total_scheduled']) }}</h4>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm border-0">
          <div class="card-body p-3">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-light-danger text-danger me-3">
                <i class="fas fa-exclamation-triangle"></i>
              </div>
              <div>
                <span class="text-muted text-uppercase fs-7 fw-semibold">Failed Deliveries</span>
                <h4 class="mb-0 fw-bold text-danger">{{ number_format($stats['total_failed']) }}</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Campaigns Card -->
    <div class="row">
      <div class="col-sm-12">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
              <h5 class="mb-0 fw-bold">Broadcast Campaigns</h5>
              <small class="text-muted">Each campaign broadcast shows as one single row with detailed recipient status on click.</small>
            </div>

            <!-- Filters Form -->
            <form method="GET" action="{{ route('admin.marketing.index') }}" class="d-flex flex-wrap align-items-center gap-2">
              <div class="input-group input-group-sm" style="width: 220px;">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Search campaign title..." value="{{ request('search') }}">
              </div>

              <select name="status" class="form-select form-select-sm" style="width: 140px;" onchange="this.form.submit()">
                <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>All Statuses</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
              </select>

              @if(request('search') || (request('status') && request('status') !== 'all'))
                <a href="{{ route('admin.marketing.index') }}" class="btn btn-outline-secondary btn-sm" title="Clear filters">
                  <i class="fas fa-times"></i>
                </a>
              @endif
            </form>
          </div>

          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th style="width: 60px;" class="ps-3">#ID</th>
                    <th>Campaign Title / Subject</th>
                    <th>Notification Date</th>
                    <th>Target</th>
                    <th>Delivery Status</th>
                    <th style="width: 220px;">Progress</th>
                    <th class="text-end pe-3" style="width: 160px;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($notifications as $item)
                  @php
                    $badge = $item->status_badge;
                    $progress = $item->progress_percentage;
                  @endphp
                  <tr>
                    <td class="ps-3 text-muted fw-bold">#{{ $item->id }}</td>
                    <td>
                      <a href="{{ route('admin.marketing.show', $item->id) }}" class="campaign-title-link">
                        {{ $item->title }}
                      </a>
                      <div class="small text-muted">
                        Created by {{ $item->creator?->name ?? 'Admin' }} &bull; {{ $item->created_at->diffForHumans() }}
                      </div>
                    </td>
                    <td>
                      <div class="fw-semibold text-dark">
                        <i class="far fa-clock text-muted me-1"></i>
                        {{ $item->scheduled_at ? $item->scheduled_at->format('d M Y, h:i A') : 'Immediate' }}
                      </div>
                      @if($item->sent_at)
                        <small class="text-success d-block">Finished: {{ $item->sent_at->format('d M Y, h:i A') }}</small>
                      @endif
                    </td>
                    <td>
                      @if($item->target_type === 'all')
                        <span class="badge bg-light-primary text-primary fw-semibold">
                          <i class="fas fa-globe-americas me-1"></i> All Restaurants ({{ $item->total_recipients }})
                        </span>
                      @else
                        <span class="badge bg-light-info text-info fw-semibold">
                          <i class="fas fa-check-double me-1"></i> {{ $item->total_recipients }} Selected
                        </span>
                      @endif
                    </td>
                    <td>
                      <span class="badge {{ $badge['class'] }} px-2 py-1">
                        {{ $badge['label'] }}
                      </span>
                    </td>
                    <td>
                      <div class="d-flex align-items-center justify-content-between small mb-1">
                        <span>
                          <strong class="text-success">{{ $item->sent_count }}</strong> sent 
                          @if($item->failed_count > 0)
                            / <strong class="text-danger">{{ $item->failed_count }}</strong> failed
                          @endif
                        </span>
                        <span class="text-muted fw-bold">{{ $progress }}%</span>
                      </div>
                      <div class="progress progress-compact">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                    </td>
                    <td class="text-end pe-3">
                      <!-- View Live Status Modal Button -->
                      <button type="button" class="btn btn-outline-primary btn-sm me-1 view-status-btn" 
                              data-id="{{ $item->id }}" 
                              title="Click to view 100+ restaurant status log">
                        <i class="fas fa-list-check"></i> Status
                      </button>

                      <a href="{{ route('admin.marketing.show', $item->id) }}" class="btn btn-light btn-sm text-secondary me-1" title="View Full Campaign & Email">
                        <i class="fas fa-eye"></i>
                      </a>

                      <form action="{{ route('admin.marketing.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this notification campaign and its logs?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-light btn-sm text-danger" title="Delete">
                          <i class="fas fa-trash-alt"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="7" class="text-center py-5">
                      <div class="text-muted">
                        <i class="fas fa-bullhorn fa-3x mb-3 text-secondary opacity-50"></i>
                        <h6 class="fw-bold">No Marketing Notifications Found</h6>
                        <p class="small text-muted mb-3">Create your first broadcast notification to send announcements or promotional emails to all restaurant owners.</p>
                        <a href="{{ route('admin.marketing.create') }}" class="btn btn-primary btn-sm">
                          <i class="fas fa-paper-plane me-1"></i> Send New Notification
                        </a>
                      </div>
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
            <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
              <div class="text-muted small">
                Showing {{ $notifications->firstItem() }} to {{ $notifications->lastItem() }} of {{ $notifications->total() }} campaigns
              </div>
              <div>
                {{ $notifications->links() }}
              </div>
            </div>
            @endif

          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Modal: Quick Campaign Status & Recipient Logs -->
<div class="modal fade" id="campaignStatusModal" tabindex="-1" aria-labelledby="campaignStatusModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light border-bottom">
        <div>
          <h5 class="modal-title fw-bold" id="campaignStatusModalLabel">
            <i class="fas fa-chart-pie text-primary me-2"></i> Broadcast Delivery Status
          </h5>
          <small class="text-muted" id="modalCampaignTitle">Loading campaign details...</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4">
        <!-- Live Counters -->
        <div class="row g-3 mb-4" id="modalCounters">
          <div class="col-sm-3">
            <div class="p-3 bg-light rounded text-center">
              <span class="text-muted small text-uppercase">Total Targets</span>
              <h4 class="mb-0 fw-bold" id="modalTotalCount">-</h4>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="p-3 bg-light-success rounded text-center text-success">
              <span class="small text-uppercase">Sent Successfully</span>
              <h4 class="mb-0 fw-bold" id="modalSentCount">-</h4>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="p-3 bg-light-danger rounded text-center text-danger">
              <span class="small text-uppercase">Failed</span>
              <h4 class="mb-0 fw-bold" id="modalFailedCount">-</h4>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="p-3 bg-light-warning rounded text-center text-warning">
              <span class="small text-uppercase">Pending in Queue</span>
              <h4 class="mb-0 fw-bold" id="modalPendingCount">-</h4>
            </div>
          </div>
        </div>

        <!-- Search in modal -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-bold mb-0">Restaurant Delivery Logs</h6>
          <input type="text" id="modalSearchInput" class="form-control form-control-sm" style="max-width: 250px;" placeholder="Search restaurants or emails...">
        </div>

        <!-- Recipients Table -->
        <div class="table-responsive" style="max-height: 400px;">
          <table class="table table-sm table-hover align-middle mb-0" id="modalRecipientsTable">
            <thead class="table-light sticky-top">
              <tr>
                <th>#</th>
                <th>Restaurant Name</th>
                <th>Owner Name</th>
                <th>Owner Email</th>
                <th>Status</th>
                <th>Sent At</th>
                <th>Remark / Error</th>
              </tr>
            </thead>
            <tbody id="modalRecipientsTbody">
              <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                  <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div> Loading recipient records...
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer bg-light border-top d-flex justify-content-between">
        <a href="#" id="modalFullDetailsLink" class="btn btn-outline-primary btn-sm">
          <i class="fas fa-external-link-alt me-1"></i> Open Full Campaign Page
        </a>
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusModalEl = document.getElementById('campaignStatusModal');
    const statusModal = new bootstrap.Modal(statusModalEl);

    let currentRecipientsList = [];

    document.querySelectorAll('.view-status-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const campaignId = this.dataset.id;
            
            // Reset modal UI
            document.getElementById('modalCampaignTitle').innerText = 'Loading campaign details...';
            document.getElementById('modalTotalCount').innerText = '-';
            document.getElementById('modalSentCount').innerText = '-';
            document.getElementById('modalFailedCount').innerText = '-';
            document.getElementById('modalPendingCount').innerText = '-';
            document.getElementById('modalRecipientsTbody').innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div> Loading recipient records...
                    </td>
                </tr>`;
            
            document.getElementById('modalFullDetailsLink').href = `/admin/marketing/${campaignId}`;

            statusModal.show();

            // Fetch AJAX status
            fetch(`/admin/marketing/${campaignId}/status-ajax`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const notif = data.notification;
                        document.getElementById('modalCampaignTitle').innerText = `Campaign: "${notif.title}" (Scheduled: ${notif.scheduled_at})`;
                        document.getElementById('modalTotalCount').innerText = notif.total_recipients;
                        document.getElementById('modalSentCount').innerText = notif.sent_count;
                        document.getElementById('modalFailedCount').innerText = notif.failed_count;
                        document.getElementById('modalPendingCount').innerText = notif.pending_count;

                        currentRecipientsList = data.recipients;
                        renderRecipients(currentRecipientsList);
                    }
                })
                .catch(err => {
                    document.getElementById('modalRecipientsTbody').innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center py-4 text-danger">
                                <i class="fas fa-exclamation-circle me-1"></i> Failed to load status. Please try opening the full campaign page.
                            </td>
                        </tr>`;
                });
        });
    });

    function renderRecipients(list) {
        const tbody = document.getElementById('modalRecipientsTbody');
        if (!list || list.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">No recipients found.</td></tr>`;
            return;
        }

        let html = '';
        list.forEach((r, idx) => {
            let statusBadge = '<span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half me-1"></i> Pending</span>';
            if (r.status === 'sent') {
                statusBadge = '<span class="badge bg-success text-white"><i class="fas fa-check me-1"></i> Sent</span>';
            } else if (r.status === 'failed') {
                statusBadge = '<span class="badge bg-danger text-white"><i class="fas fa-times me-1"></i> Failed</span>';
            }

            const sentTime = r.sent_at ? new Date(r.sent_at).toLocaleString() : '-';
            const errorMsg = r.error_message ? `<span class="text-danger small" title="${r.error_message}">${r.error_message.substring(0, 40)}...</span>` : '<span class="text-muted">-</span>';

            html += `
                <tr>
                    <td class="text-muted small">${idx + 1}</td>
                    <td class="fw-semibold">${r.restaurant_name || 'N/A'}</td>
                    <td>${r.owner_name || 'N/A'}</td>
                    <td><a href="mailto:${r.email}" class="text-primary">${r.email}</a></td>
                    <td>${statusBadge}</td>
                    <td class="small text-muted">${sentTime}</td>
                    <td>${errorMsg}</td>
                </tr>`;
        });

        tbody.innerHTML = html;
    }

    // Modal Search Filter
    const searchInput = document.getElementById('modalSearchInput');
    searchInput.addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        if (!q) {
            renderRecipients(currentRecipientsList);
            return;
        }

        const filtered = currentRecipientsList.filter(r => {
            return (r.restaurant_name && r.restaurant_name.toLowerCase().includes(q)) ||
                   (r.owner_name && r.owner_name.toLowerCase().includes(q)) ||
                   (r.email && r.email.toLowerCase().includes(q));
        });

        renderRecipients(filtered);
    });
});
</script>
@endsection
