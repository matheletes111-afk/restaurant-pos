@extends('layouts.app')

@section('title')
<title>Admin || Manage Role</title>
@endsection

@section('style')
@include('includes.style')
<style type="text/css">
    label{
        font-size: 16px;
        font-weight: bolder;
        color: red;
        margin-bottom: 5px;
    }

    .uplodimgfilimg {
    margin-left: 20px;
    padding-top: 20px;
}
.uplodimgfilimg em {
    width: 58px;
    height: 58px;
    position: relative;
    display: inline-block;
    overflow: hidden;
    border-radius: 4px;
}

 .uplodimgfilimg em img{
    position: absolute;
    max-width: 100%;
    max-height: 100%;
  }
</style>
@endsection


@section('body')

@section('content')

    <div class="container-xxl position-relative bg-white d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->
        @include('includes.sidebar')

        @include('includes.navbar')

        

            <div class="container-fluid pt-4 px-4">
            <div class="row">
                <div class="col-12">
                     <div class="bg-light rounded h-100 p-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="mb-0">Edit Role</h6>
                                <a href="{{route('manage.role')}}">Back</a>
                        </div>
                        @include('includes.message')
                        <form role="form" action="{{ route('manage.role.manage.permission.update.decision') }}" id="frm" method="post" enctype="multipart/form-data" style="margin-bottom: 15px;">
    @csrf
        <input type="hidden" name="role_id" value="{{@$role_id}}">

        <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" name="permissions[]" value="1" id="permission1" {{ in_array(1, $selected) ? 'checked' : '' }}>
        <label class="form-check-label" for="permission1">Banner Management</label>
    </div>

    <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" name="permissions[]" value="2" id="permission2" {{ in_array(2, $selected) ? 'checked' : '' }}>
        <label class="form-check-label" for="permission2">Gallery Management</label>
    </div>

    <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" name="permissions[]" value="3" id="permission3" {{ in_array(3, $selected) ? 'checked' : '' }}>
        <label class="form-check-label" for="permission3">Services Management</label>
    </div>

    <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" name="permissions[]" value="4" id="permission4" {{ in_array(4, $selected) ? 'checked' : '' }}>
        <label class="form-check-label" for="permission4">Testimonials Management</label>
    </div>

    <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" name="permissions[]" value="5" id="permission5" {{ in_array(5, $selected) ? 'checked' : '' }}>
        <label class="form-check-label" for="permission5">Manage Banquet</label>
    </div>

    <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" name="permissions[]" value="6" id="permission6" {{ in_array(6, $selected) ? 'checked' : '' }}>
        <label class="form-check-label" for="permission6">Manage Role</label>
    </div>

    <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" name="permissions[]" value="7" id="permission7" {{ in_array(7, $selected) ? 'checked' : '' }}>
        <label class="form-check-label" for="permission7">Manage Staff</label>
    </div>

    <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" name="permissions[]" value="8" id="permission8" {{ in_array(8, $selected) ? 'checked' : '' }}>
        <label class="form-check-label" for="permission8">Event Questions</label>
    </div>

    <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" name="permissions[]" value="9" id="permission9" {{ in_array(9, $selected) ? 'checked' : '' }}>
        <label class="form-check-label" for="permission9">Manage Food Menu</label>
    </div>

    <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" name="permissions[]" value="10" id="permission10" {{ in_array(10, $selected) ? 'checked' : '' }}>
        <label class="form-check-label" for="permission10">Booking Availability</label>
    </div>

    <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" name="permissions[]" value="11" id="permission11" {{ in_array(11, $selected) ? 'checked' : '' }}>
        <label class="form-check-label" for="permission11">Booking Enquiry</label>
    </div>

    <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" name="permissions[]" value="12" id="permission12" {{ in_array(12, $selected) ? 'checked' : '' }}>
        <label class="form-check-label" for="permission12">Booking Manage</label>
    </div>

    <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" name="permissions[]" value="13" id="permission13" {{ in_array(13, $selected) ? 'checked' : '' }}>
        <label class="form-check-label" for="permission13">Booking Approval</label>
    </div>

    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="permissions[]" value="14" id="permission14" {{ in_array(14, $selected) ? 'checked' : '' }}>
        <label class="form-check-label" for="permission14">Notification Dashboard</label>
    </div>

    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="permissions[]" value="15" id="permission14" {{ in_array(15, $selected) ? 'checked' : '' }}>
        <label class="form-check-label" for="permission14">Rate Management</label>
    </div>

    <button type="submit" class="btn btn-primary">Update Permission</button>
</form>

                     </div>
                </div>
            </div>

              
             

            @include('includes.footer')
       

    </div>



@endsection


@section('script')
@include('includes.script')

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        function fun1(){
        var i=document.getElementById('icon').files[0];
        var b=URL.createObjectURL(i);
        $("#img2").attr("src",b);
    }
</script> 






@endsection


@endsection