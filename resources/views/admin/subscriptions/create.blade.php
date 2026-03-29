<!DOCTYPE html>
<html lang="en">
<head>
    <title>Subscribe to {{ $plan->name }}</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
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
                                <h5 class="m-b-10">Subscribe to {{ $plan->name }}</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('select.plan.page') }}">Plans</a></li>
                                <li class="breadcrumb-item" aria-current="page">Subscribe</li>
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
                            <h5>Subscription Details</h5>
                        </div>
                        <div class="card-body">
                            @include('includes.message')
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5>Plan Details</h5>
                                            <table class="table">
                                                <tr>
                                                    <th>Plan Name:</th>
                                                    <td>{{ $plan->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Price:</th>
                                                    <td>{{ number_format($plan->price, 2) }} {{ $plan->currency }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Billing Cycle:</th>
                                                    <td>{{ ucfirst($plan->billing_cycle) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Duration:</th>
                                                    <td>{{ $plan->duration_days }} days</td>
                                                </tr>
                                                <tr>
                                                    <th>Description:</th>
                                                    <td>{{ $plan->description ?? 'N/A' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5>Your Information</h5>
                                            <table class="table">
                                                <tr>
                                                    <th>Name:</th>
                                                    <td>{{ $user->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Email:</th>
                                                    <td>{{ $user->email }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Phone:</th>
                                                    <td>{{ $user->phone ?? 'Not set' }}</td>
                                                </tr>
                                            </table>
                                            
                                            @if(!$user->phone)
                                            <div class="alert alert-warning">
                                                <i class="fa fa-exclamation-triangle"></i> 
                                                Phone number is required for payments. Please update your profile.
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <form action="{{ route('admin.subscriptions.store', $plan->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                    
                                    @if(!$user->phone)
                                        <button type="button" class="btn btn-warning" disabled>
                                            <i class="fa fa-credit-card"></i> Complete Profile to Subscribe
                                        </button>
                                        <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary">
                                            <i class="fa fa-user"></i> Update Profile
                                        </a>
                                    @else
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fa fa-credit-card"></i> Proceed to Payment
                                        </button>
                                    @endif
                                    
                                    <a href="{{ route('select.plan.page') }}" class="btn btn-secondary btn-lg">
                                        <i class="fa fa-arrow-left"></i> Back to Plans
                                    </a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    @include('includes.script')
</body>
</html>