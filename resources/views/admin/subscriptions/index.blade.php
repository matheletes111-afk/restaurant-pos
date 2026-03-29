<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Subscriptions</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>
<body data-pc-theme="light">
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

    @include('includes.sidebar')

    <div class="pc-container">
        <div class="pc-content">
            <!-- Breadcrumb -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">My Subscriptions</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="">Home</a></li>
                                <li class="breadcrumb-item" aria-current="page">Subscriptions</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Breadcrumb end -->

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ route('plans.index') }}" class="btn btn-primary" style="float: right;">
                                <i class="fa fa-plus"></i> View Plans
                            </a>
                        </div>
                        <div class="card-body">
                            @include('includes.message')
                            
                            <div class="dt-responsive table-responsive">
                                <table id="subscriptionsTable" class="table table-striped table-bordered nowrap">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Plan</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Renewal Date</th>
                                            <th>Auto Renew</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subscriptions as $subscription)
                                        <tr>
                                            <td>{{ $subscription->id }}</td>
                                            <td>{{ $subscription->plan->name ?? 'N/A' }}</td>
                                            <td>{{ number_format($subscription->plan->price ?? 0, 2) }}</td>
                                            <td>
                                                <span class="badge badge-{{ 
                                                    $subscription->status == 'active' ? 'success' : 
                                                    ($subscription->status == 'cancelled' ? 'danger' : 
                                                    ($subscription->status == 'expired' ? 'warning' : 'secondary')) 
                                                }}">
                                                    {{ ucfirst($subscription->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $subscription->start_date->format('Y-m-d') }}</td>
                                            <td>{{ $subscription->end_date->format('Y-m-d') }}</td>
                                            <td>{{ $subscription->renewal_date ? $subscription->renewal_date->format('Y-m-d') : 'N/A' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $subscription->auto_renew ? 'success' : 'secondary' }}">
                                                    {{ $subscription->auto_renew ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($subscription->status == 'active')
                                                <button class="btn btn-danger btn-sm cancel-btn" 
                                                        data-id="{{ $subscription->id }}" 
                                                        data-plan="{{ $subscription->plan->name ?? 'N/A' }}">
                                                    <i class="fa fa-ban"></i> Cancel
                                                </button>
                                                @else
                                                <span class="text-muted">No actions</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Confirm Cancellation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to cancel your subscription to "<span id="planName"></span>"?
                    <div class="alert alert-warning mt-2">
                        <i class="fa fa-exclamation-triangle"></i> 
                        This action cannot be undone. Any remaining days will not be refunded.
                    </div>
                </div>
                <div class="modal-footer">
                    <form id="cancelForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Cancel Subscription</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Keep Subscription</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#subscriptionsTable').DataTable({
                order: [[0, 'desc']]
            });

            // Cancel button
            $('.cancel-btn').on('click', function() {
                let id = $(this).data('id');
                let planName = $(this).data('plan');
                
                $('#planName').text(planName);
                $('#cancelForm').attr('action', '{{ url("admin/subscriptions") }}/' + id + '/cancel');
                $('#cancelModal').modal('show');
            });
        });
    </script>
    
    @include('includes.script')
</body>
</html>