<script src="{{asset('admin_template/js/plugins/popper.min.js')}}"></script>
<script src="{{asset('admin_template/js/plugins/simplebar.min.js')}}"></script>
<script src="{{asset('admin_template/js/plugins/bootstrap.min.js')}}"></script>
<script src="{{asset('admin_template/js/fonts/custom-font.js')}}"></script>
<script src="{{asset('admin_template/js/pcoded.js')}}"></script>
<script src="{{asset('admin_template/js/plugins/feather.min.js')}}"></script>

@auth
    @include('includes.qr_notifications_script')
    @include('includes.firebase_push')
@endauth
