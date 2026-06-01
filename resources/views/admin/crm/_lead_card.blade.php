@php
    $badgeClass = 'badge-default';
    if ($lead->source == 'Social Media') {
        $badgeClass = 'badge-social';
    } elseif ($lead->source == 'Search Engine') {
        $badgeClass = 'badge-search';
    } elseif ($lead->source == 'Friend/Colleague') {
        $badgeClass = 'badge-friend';
    }
@endphp

<div class="lead-card draggable-card" draggable="true" data-lead-id="{{ $lead->id }}" data-status="{{ $lead->status }}">
    
    <!-- Source Badge -->
    <span class="lead-badge {{ $badgeClass }}">
        {{ $lead->source ?? 'Direct Lead' }}
    </span>
    
    <!-- Lead Contact Info -->
    <div class="lead-name">
        <a href="{{ route('admin.crm.show', $lead->id) }}" style="color: inherit; text-decoration: none;" class="hover-underline">
            {{ $lead->full_name }}
        </a>
    </div>
    
    @if($lead->restaurant_name)
        <div class="lead-restaurant">
            <i class="fa-solid fa-store text-muted"></i> {{ $lead->restaurant_name }}
        </div>
    @else
        <div class="lead-restaurant">
            <i class="fa-solid fa-store text-muted"></i> <i>No Restaurant Specified</i>
        </div>
    @endif
    
    <div class="lead-details">
        <a href="mailto:{{ $lead->email_address }}">
            <i class="fa-regular fa-envelope me-1 text-muted"></i> {{ $lead->email_address }}
        </a>
        @if($lead->phone_number)
            <a href="tel:{{ $lead->phone_number }}" class="mt-1">
                <i class="fa-solid fa-phone me-1 text-muted"></i> {{ $lead->phone_number }}
            </a>
        @endif
    </div>

    <!-- Followup Box if scheduled -->
    @if($lead->followup_date)
        <div class="followup-box">
            <div class="followup-header">
                <i class="fa-regular fa-bell"></i> 
                {{ \Carbon\Carbon::parse($lead->followup_date)->format('d M Y, h:i A') }}
            </div>
            @if($lead->followup_notes)
                <div class="followup-notes-text" title="{{ $lead->followup_notes }}">
                    {{ $lead->followup_notes }}
                </div>
            @else
                <div class="text-muted italic" style="font-size: 0.7rem; font-style: italic;">No notes added</div>
            @endif
        </div>
    @endif

    <!-- Move Stage & Followup Actions -->
    <div class="card-actions">
        <select class="status-select select-style">
            <option value="Contacted" {{ $lead->status == 'Contacted' ? 'selected' : '' }}>Move to Contacted</option>
            <option value="Qualified" {{ $lead->status == 'Qualified' ? 'selected' : '' }}>Move to Qualified</option>
            <option value="Nurturing" {{ $lead->status == 'Nurturing' ? 'selected' : '' }}>Move to Nurturing</option>
            <option value="Converted" {{ $lead->status == 'Converted' ? 'selected' : '' }}>Move to Converted</option>
            <option value="Lost" {{ $lead->status == 'Lost' ? 'selected' : '' }}>Move to Lost</option>
        </select>
        
        <button type="button" class="action-icon-btn btn-arrow" title="Update Stage">
            <i class="fa-solid fa-arrow-right"></i>
        </button>
        
        <button type="button" class="action-icon-btn edit-followup-btn" 
                data-id="{{ $lead->id }}" 
                data-name="{{ $lead->full_name }}" 
                data-date="{{ $lead->followup_date ? $lead->followup_date->toIso8601String() : '' }}" 
                data-notes="{{ $lead->followup_notes }}" 
                title="Schedule Follow-up">
            <i class="fa-regular fa-calendar-plus"></i>
        </button>
    </div>
</div>
