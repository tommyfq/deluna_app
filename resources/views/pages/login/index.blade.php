<div class="login-form-bg h-100">
    <div class="container h-100">
        <div class="row justify-content-center h-100">
            <div class="col-xl-6">
                <div class="form-input-content">
                    <div class="card login-form mb-0">
                        <div class="card-body pt-5">
                            @include('_includes.alert')
                            <a class="text-center" href="javascript:void()"> <h4>Deluna</h4></a>
                            <form class="mt-5 mb-5 login-input form-valide" action="{{route('login.post')}}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <div class="col-12">
                                        <input type="email" id="email" name="email" class="form-control" placeholder="Email">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-12">
                                        <input type="password" id="password" name="password" class="form-control" placeholder="Password">
                                    </div>
                                </div>
                                <button class="btn login-form__btn submit w-100">Sign In</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script src="{{asset('plugins/validation/jquery.validate.min.js')}}"></script>
    <script>
        jQuery(".form-valide").validate({
            ignore: [],
            errorClass: "invalid-feedback animated fadeInDown",
            errorElement: "div",
            errorPlacement: function (e, a) {
                jQuery(a).parents(".form-group > div").append(e);
            },
            highlight: function (e) {
                jQuery(e).closest(".form-group").removeClass("is-invalid").addClass("is-invalid");
            },
            success: function (e) {
                jQuery(e).closest(".form-group").removeClass("is-invalid"), jQuery(e).remove();
            },
            rules: {
                "email": { required: !0 },
                "password": { required: !0 },
            },
            messages: {
                "email": "Please enter a valid email address",
                "password": { required: "Please provide a password", minlength: "Your password must be at least 5 characters long" },
            },
        });
    </script>
@endpush