<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    @include('_includes.alert')
                    <div class="form-validation">
                        <form class="form-valide" action="{{route('user.update',[$data->id])}}" method="post">
                            @method('put')
                            @csrf
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="username">Name <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="username" name="name" value="{{$data->name}}" placeholder="Enter your username">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="email">Email <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6">
                                    <input type="email" class="form-control" id="email" name="email" value="{{$data->email}}" placeholder="Enter your Email">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="password">Password <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="is_active">Active <span class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <label class="radio-inline mr-3">
                                        <input type="radio" name="is_active" value="true" {{ old('is_active') ? (old('is_active') == 'true' ? 'checked' : '') : ($data->is_active ? 'checked' : '' ) }}>Active</label>
                                    <label class="radio-inline mr-3">
                                        <input type="radio" name="is_active" value="false" {{ old('is_active') ? (old('is_active') == 'false' ? 'checked' : '') : (!$data->is_active ? 'checked' : '' ) }}>Inactive</label>
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
                "password": { minlength: 6 },
                "is_active": { required: !0 },
            },
            messages: {
                "name": { required: "Please enter a name", minlength: "Your name must consist of at least 3 characters" },
                "email": "Please enter a valid email address",
                "password": { minlength: "Your password must be at least 6 characters long" },
                "is_active": "Please choose the active status",
            },
        });
    </script>
@endpush