<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    @include('_includes.alert')
                    <div class="form-validation">
                        <form class="form-valide" action="{{route($_page.'.store')}}" method="post">
                            @csrf
                            <div class="form-group row">
                                <div class="col-lg-12 ml-auto text-right">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="name">Customer Name <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your product name" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="description">Customer Phone
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="description" name="description" placeholder="Enter your product description" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="description">Customer Email
                                </label>
                                <div class="col-lg-6">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your product description" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="description">Address
                                </label>
                                <div class="col-lg-6">
                                    <textarea class="form-control" id="address" name="address" placeholder="Enter Address">{{old('address')}}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="category">Sales Channel <span class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <select class="form-control" id="category" name="category" required>
                                        <option value="">Select Sales Channel</option>
                                        @if($_sales_channel)
                                            @foreach($_sales_channel as $val)
                                                <option value="{{$val->id}}">{{$val->name}}</option>
                                            @endforeach
                                        @endif
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
                            <hr>
                            <div class="option-container">
                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control option-name" name="options[]" placeholder="Enter options" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-6">
                                    <button id="btn-add" type="button" class="btn btn-info btn-block">+ Add Option</button>
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
    <script>
        $(document).ready(function(){
            $('#btn-add').on('click',function(){
                $('.option-container').append(`
                    <div class="form-group row option-list">
                        <div class="col-lg-6">
                            <input type="text" class="form-control option-name" name="options[]" placeholder="Enter options" required>
                        </div>
                        <div class="col-lg-1">
                            <button type="button" class="btn btn-danger btn-del-option"> <i class="fa fa-trash"></i></button>
                        </div>
                    </div>`
                );
            });

            $(document).on('click','.btn-del-option',function(){
                $(this).closest('.option-list').remove();
            })
        });
    </script>
@endpush