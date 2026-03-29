<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Restaurant</title>
    @include('includes.style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>

<body>
@include('includes.sidebar')

<div class="pc-container">
<div class="pc-content">

    <div class="page-header">
        <h5 class="m-b-10">Manage Restaurant</h5>
    </div>

    <div class="card">
        @include('includes.message')

        {{-- <div class="card-header">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addRestaurantModal" style="float:right;">
                <i class="fa fa-plus"></i> Add Restaurant
            </button>
        </div> --}}

        <div class="card-body">
            <div class="table-responsive">

                <table id="restaurantTable" class="table table-striped table-bordered nowrap">
                    <thead>
                    <tr>
                        <th>Restaurant</th>
                        <th>Address</th>
                        <th>Pincode</th>
                        <th>Owner</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($restaurants as $rest)
                        <tr>

                            <td>{{ $rest->name }}</td>
                            <td>{{ $rest->address }}</td>
                            <td>{{ $rest->pincode }}</td>

                            <td>{{ $rest->owner->name ?? '' }}</td>
                            <td>{{ $rest->owner->email ?? '' }}</td>
                            <td>{{ $rest->owner->phone ?? '' }}</td>

                            <td>
                                <a href="{{ route('manage.restaurant.status', $rest->owner_id) }}"
                                   onclick="return confirm('Are you sure?')"
                                   class="btn btn-sm {{ $rest->status == 'A' ? 'btn-success' : 'btn-warning' }}">

                                    {{ $rest->status == 'A' ? 'Active' : 'Inactive' }}
                                </a>
                            </td>

                            <td>
                                <!-- EDIT BUTTON -->
                                <button class="btn btn-success editBtn"
                                        data-id="{{ $rest->id }}"
                                        data-owner_id="{{ $rest->owner_id }}"
                                        data-restaurant_name="{{ $rest->name }}"
                                        data-restaurant_address="{{ $rest->address }}"
                                        data-restaurant_pincode="{{ $rest->pincode }}"
                                        data-owner_name="{{ @$rest->owner->name }}"
                                        data-owner_email="{{ @$rest->owner->email }}"
                                        data-owner_phone="{{ @$rest->owner->phone }}">
                                    <i class="fa fa-edit"></i>
                                </button>

                                <a href="{{ route('restaurant.analytics', $rest->id) }}"
                                   
                                   class="btn btn-primary">
                                    <i class="fa fa-handshake"></i>
                                </a>

                                <!-- DELETE BUTTON -->
                                <a href="{{ route('manage.restaurant.delete', $rest->id) }}"
                                   onclick="return confirm('Delete this restaurant?')"
                                   class="btn btn-danger">
                                    <i class="fa fa-trash"></i>
                                </a>

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


<!-- =======================================================
                     ADD MODAL
======================================================== -->
<div class="modal fade" id="addRestaurantModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form action="{{ route('manage.restaurant.insert') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add Restaurant</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body row">

                    <div class="col-md-6 mt-2">
                        <label>Restaurant Name</label>
                        <input type="text" name="restaurant_name" class="form-control" required>
                    </div>

                    <div class="col-md-6 mt-2">
                        <label>Pincode</label>
                        <input type="text" name="pincode" class="form-control" required>
                    </div>

                    <div class="col-md-12 mt-2">
                        <label>Address</label>
                        <input type="text" name="address" class="form-control" required>
                    </div>

                    <div class="col-md-6 mt-2">
                        <label>Owner Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="col-md-6 mt-2">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="col-md-6 mt-2">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>

                    <div class="col-md-6 mt-2">
                        <label>Password</label>
                        <input type="text" name="password" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>

            </form>

        </div>
    </div>
</div>



<!-- =======================================================
                     EDIT MODAL
======================================================== -->
<div class="modal fade" id="editRestaurantModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form action="{{ route('manage.restaurant.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="owner_id" id="edit_owner_id">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Restaurant</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body row">

                    <div class="col-md-6 mt-2">
                        <label>Restaurant Name</label>
                        <input type="text" name="restaurant_name" id="edit_restaurant_name" class="form-control" required>
                    </div>

                    <div class="col-md-6 mt-2">
                        <label>Pincode</label>
                        <input type="text" name="pincode" id="edit_restaurant_pincode" class="form-control" required>
                    </div>

                    <div class="col-md-12 mt-2">
                        <label>Address</label>
                        <input type="text" name="address" id="edit_restaurant_address" class="form-control" required>
                    </div>

                    <div class="col-md-6 mt-2">
                        <label>Owner Name</label>
                        <input type="text" name="name" id="edit_owner_name" class="form-control" required>
                    </div>

                    <div class="col-md-6 mt-2">
                        <label>Email</label>
                        <input type="email" name="email" id="edit_owner_email" class="form-control" required>
                    </div>

                    <div class="col-md-6 mt-2">
                        <label>Phone</label>
                        <input type="text" name="phone" id="edit_owner_phone" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>

            </form>

        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@include('includes.script')

<script>
$(document).ready(function () {

    $('#restaurantTable').DataTable();

    $('.editBtn').on('click', function () {

        $('#edit_id').val($(this).data('id'));
        $('#edit_owner_id').val($(this).data('owner_id'));

        $('#edit_restaurant_name').val($(this).data('restaurant_name'));
        $('#edit_restaurant_address').val($(this).data('restaurant_address'));
        $('#edit_restaurant_pincode').val($(this).data('restaurant_pincode'));

        $('#edit_owner_name').val($(this).data('owner_name'));
        $('#edit_owner_email').val($(this).data('owner_email'));
        $('#edit_owner_phone').val($(this).data('owner_phone'));

        $('#editRestaurantModal').modal('show');
    });

});
</script>

</body>
</html>
