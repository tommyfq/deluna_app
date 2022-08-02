<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    @include('_includes.alert')
                    <div class="form-validation">
                        <form class="form-valide" action="{{route('user.store')}}" method="post">
                            @csrf
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="username">Name <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="username" name="name" value="{{old('name')}}" placeholder="Enter your username">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="email">Email <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6">
                                    <input type="email" class="form-control" id="email" name="email" value="{{old('email')}}" placeholder="Enter your Email">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="password">Password <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6">
                                    <input type="password" class="form-control" id="password" name="password" value="{{old('password')}}" placeholder="Enter your password">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="confirm-password">Confirm Password <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" value="{{old('confirm_password')}}" placeholder="Enter your confirm paswword">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="role">Role <span class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <select class="form-control" id="role" name="role">
                                        <option value=""> Select Role... </option>
                                        @foreach($_roles as $val)
                                            <option value="{{$val->id}}">{{$val->role_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="is_active">Active <span class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <label class="radio-inline mr-3">
                                        <input type="radio" name="is_active" value="true" checked>Active</label>
                                    <label class="radio-inline mr-3">
                                        <input type="radio" name="is_active" value="false">Inactive</label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-8 ml-auto">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
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
                "name": { required: !0, minlength: 3 },
                "email": { required: !0, email: !0 },
                "password": { required: !0, minlength: 6 },
                "confirm_password": { required: !0, equalTo: "#password" },
                "role": { required: !0 },
                "is_active": { required: !0 }
            },
            messages: {
                "name": { required: "Please enter a name", minlength: "Your name must consist of at least 3 characters" },
                "email": "Please enter a valid email address",
                "password": { required: "Please provide a password", minlength: "Your password must be at least 6 characters long" },
                "confirm_password": { required: "Please provide a password", minlength: "Your password must be at least 6 characters long", equalTo: "Please enter the same password as above" },
                "role": "Please choose the role",
                "is_active": "Please choose the active status",
            },
        });
    </script>
@endpush