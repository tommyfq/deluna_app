<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    @include('_includes.alert')
                    <div class="form-validation">
                        <form class="form-valide" action="{{route('vendor.store')}}" method="post">
                            @csrf
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="username">Name <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="username" name="name" placeholder="Enter Vendor Name">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="slug">Slug <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6">
                                    <input type="email" class="form-control" id="slug" name="slug" placeholder="Enter Slug">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="address">Address <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6">
                                    <textarea class="form-control" id="address" name="address" placeholder="Enter Address"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="description">Description <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6">
                                    <textarea class="form-control" id="description" name="description" placeholder="Enter Description"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="phone">Phone <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter Phone">
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
            },
            messages: {
                "name": { required: "Please enter a name", minlength: "Your name must consist of at least 3 characters" },
                "email": "Please enter a valid email address",
                "password": { required: "Please provide a password", minlength: "Your password must be at least 6 characters long" },
                "confirm_password": { required: "Please provide a password", minlength: "Your password must be at least 6 characters long", equalTo: "Please enter the same password as above" },
            },
        });
    </script>
@endpush