@extends('layouts.app')

@section('title')
<title>Admin || Campaign Details - {{ $notification->title }}</title>
@endsection

@section('style')
@include('includes.style')
<style>
    .stat-box {
        border-radius: 10px;
        padding: 16px;
        text-align: center;
        border: 1px solid #e2e8f0;
    }
    .email-preview-container {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background-color: #f8fafc;
        padding: 20px;
        max-height: 500px;
        overflow-y: auto;
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
              <h5 class="m-b-10">Broadcast Campaign Details</h5>
            </div>
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('admin.marketing.index') }}">Marketing Notifications</a></li>
              <li class="breadcrumb-item" aria-current="page">Campaign #{{ $notification->id }}</li>
            </ul>
          </div>
          <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <a href="{{ route('admin.marketing.index') }}" class="btn btn-outline-secondary btn-sm me-1">
              &larr; Back to Campaigns
            </a>
            <a href="{{ route('admin.marketing.create') }}" class="btn btn-primary btn-sm">
              <i class="fas fa-plus me-1"></i> New Notification
            </a>
          </div>
        </div>
      </div>
    </div>
    <!-- End Breadcrumb -->

    @include('includes.message')

    @php
      $badge = $notification->status_badge;
      $progress = $notification->progress_percentage;
    @endphp

    <!-- Campaign Header Card -->
    <div class="card shadow-sm border-0 mb-4">
      <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
          <div>
            <div class="d-flex align-items-center gap-2 mb-2">
              <span class="badge {{ $badge['class'] }} px-3 py-1 fs-7">
                {{ $badge['label'] }}
              </span>
              <span class="text-muted small">Campaign ID #{{ $notification->id }}</span>
            </div>
            <h3 class="fw-bold mb-1 text-dark">{{ $notification->title }}</h3>
            <div class="text-muted small">
              <i class="far fa-user me-1"></i> Created by <strong>{{ $notification->creator?->name ?? 'Admin' }}</strong> &bull;
              <i class="far fa-calendar-alt ms-2 me-1"></i> Created on {{ $notification->created_at->format('d M Y, h:i A') }} &bull;
              <i class="far fa-clock ms-2 me-1"></i> Scheduled: <strong>{{ $notification->scheduled_at ? $notification->scheduled_at->format('d M Y, h:i A') : 'Immediate' }}</strong>
              @if($notification->sent_at)
                &bull; <i class="fas fa-check-double text-success ms-2 me-1"></i> Finished: {{ $notification->sent_at->format('d M Y, h:i A') }}
              @endif
            </div>
          </div>

          <!-- Actions -->
          <div class="d-flex gap-2 align-items-center">
            @if($breakdown['failed'] > 0)
            <form action="{{ route('admin.marketing.retry', $notification->id) }}" method="POST" onsubmit="return confirm('Retry sending to {{ $breakdown['failed'] }} failed recipients?');">
              @csrf
              <button type="submit" class="btn btn-warning btn-sm text-dark shadow-sm">
                <i class="fas fa-redo me-1"></i> Retry {{ $breakdown['failed'] }} Failed
              </button>
            </form>
            @endif

            <form action="{{ route('admin.marketing.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('Delete this entire notification campaign?');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-trash-alt me-1"></i> Delete
              </button>
            </form>
          </div>
        </div>

        <!-- Metric Counter Tiles -->
        <div class="row g-3 mt-3">
          <div class="col-sm-6 col-lg-3">
            <div class="stat-box bg-light">
              <span class="text-muted small text-uppercase fw-semibold">Total Target Restaurants</span>
              <h3 class="mb-0 fw-bold text-dark mt-1">{{ number_format($breakdown['total']) }}</h3>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="stat-box bg-light-success text-success border-success border-opacity-25">
              <span class="small text-uppercase fw-semibold">Delivered Successfully</span>
              <h3 class="mb-0 fw-bold mt-1">{{ number_format($breakdown['sent']) }}</h3>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="stat-box bg-light-danger text-danger border-danger border-opacity-25">
              <span class="small text-uppercase fw-semibold">Failed Deliveries</span>
              <h3 class="mb-0 fw-bold mt-1">{{ number_format($breakdown['failed']) }}</h3>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="stat-box bg-light-warning text-warning border-warning border-opacity-25">
              <span class="small text-uppercase fw-semibold">Pending In Queue</span>
              <h3 class="mb-0 fw-bold mt-1">{{ number_format($breakdown['pending']) }}</h3>
            </div>
          </div>
        </div>

        <!-- Progress bar -->
        <div class="mt-3">
          <div class="d-flex justify-content-between small text-muted mb-1">
            <span>Overall Delivery Completion</span>
            <span class="fw-bold">{{ $progress }}%</span>
          </div>
          <div class="progress" style="height: 8px;">
            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>

      </div>
    </div>

    <!-- Main Content Tabs / Sections -->
    <div class="row">
      <!-- Left Column: Recipient Delivery Log Table -->
      <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
              <h5 class="mb-0 fw-bold">Recipient Delivery Logs</h5>
              <small class="text-muted">Live delivery status for each restaurant owner.</small>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ route('admin.marketing.show', $notification->id) }}" class="d-flex align-items-center gap-2">
              <div class="input-group input-group-sm" style="width: 200px;">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name / email..." value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
              </div>

              <select name="status" class="form-select form-select-sm" style="width: 120px;" onchange="this.form.submit()">
                <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>All</option>
                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
              </select>

              @if(request('search') || (request('status') && request('status') !== 'all'))
                <a href="{{ route('admin.marketing.show', $notification->id) }}" class="btn btn-outline-secondary btn-sm">
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
                    <th style="width: 40px;" class="ps-3">#</th>
                    <th>Restaurant</th>
                    <th>Owner / Contact</th>
                    <th>Status</th>
                    <th>Sent Timestamp</th>
                    <th class="pe-3">Notes</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($recipients as $idx => $r)
                  <tr>
                    <td class="ps-3 text-muted small">{{ $recipients->firstItem() + $idx }}</td>
                    <td>
                      <div class="fw-semibold text-dark">{{ $r->restaurant_name ?? 'N/A' }}</div>
                    </td>
                    <td>
                      <div class="text-dark">{{ $r->owner_name ?? 'Valued Partner' }}</div>
                      <div class="small"><a href="mailto:{{ $r->email }}" class="text-primary">{{ $r->email }}</a></div>
                    </td>
                    <td>
                      @if($r->status === 'sent')
                        <span class="badge bg-success text-white"><i class="fas fa-check me-1"></i> Sent</span>
                      @elseif($r->status === 'failed')
                        <span class="badge bg-danger text-white"><i class="fas fa-times me-1"></i> Failed</span>
                      @else
                        <span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half me-1"></i> Pending</span>
                      @endif
                    </td>
                    <td>
                      <span class="small text-muted">
                        {{ $r->sent_at ? $r->sent_at->format('d M Y, h:i A') : '-' }}
                      </span>
                    </td>
                    <td class="pe-3">
                      @if($r->error_message)
                        <span class="text-danger small" title="{{ $r->error_message }}">
                          {{ Str::limit($r->error_message, 45) }}
                        </span>
                      @else
                        <span class="text-muted small">-</span>
                      @endif
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                      No recipients matching your search.
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            @if($recipients->hasPages())
            <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
              <div class="text-muted small">
                Showing {{ $recipients->firstItem() }} to {{ $recipients->lastItem() }} of {{ $recipients->total() }} recipients
              </div>
              <div>
                {{ $recipients->links() }}
              </div>
            </div>
            @endif

          </div>
        </div>
      </div>

      <!-- Right Column: Email Body Preview -->
      <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4 sticky-top" style="top: 80px;">
          <div class="card-header bg-white py-3 border-bottom">
            <h5 class="mb-0 fw-bold">
              <i class="fas fa-eye text-primary me-2"></i> Email Message Content
            </h5>
          </div>
          <div class="card-body p-4">
            <div class="email-preview-container">
              <div class="mb-2 pb-2 border-bottom">
                <span class="text-muted small d-block">Subject:</span>
                <strong class="text-dark">{{ $notification->title }}</strong>
              </div>
              <div class="email-rendered-html">
                {!! $notification->description !!}
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>
</div>
@endsection
