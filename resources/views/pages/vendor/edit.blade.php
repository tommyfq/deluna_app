<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    @include('_includes.alert')
                    <div class="form-validation">
                        <form class="form-valide" action="{{route($_page.'.update',[$data->id])}}" method="post">
                            @method('put')
                            @csrf
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="name">Name <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter Vendor Name" value="{{old('name') ? old('name') : $data->name}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="slug">Slug <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="slug" name="slug" placeholder="Enter Slug" value={{old('slug') ? old('slug') : $data->slug}}>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="phone">Phone <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6 input-group mb-3">
                                    <div class="input-group-prepend"><span class="input-group-text">+62</span>
                                    </div>
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter Phone" value={{old('phone') ? old('phone') : $data->phone}}>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="address">Address <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6">
                                    <textarea class="form-control" id="address" name="address" placeholder="Enter Address">{{old('address') ? old('address') : $data->address}}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="description">Description
                                </label>
                                <div class="col-lg-6">
                                    <textarea class="form-control" id="description" name="description" placeholder="Enter Description">{{old('description') ? old('description') : $data->description}}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="is_active">Active</label>
                                <div class="col-lg-6">
                                    @if(old('is_active'))
                                        <label class="radio-inline mr-3">
                                            <input type="radio" name="is_active" value="true" {{old('is_active') =='true' ? 'checked' : ''}}>Active</label>
                                        <label class="radio-inline mr-3">
                                            <input type="radio" name="is_active" value="false" {{old('is_active') =='false' ? 'checked' : ''}}>Inactive</label>
                                    @else
                                        <label class="radio-inline mr-3">
                                            <input type="radio" name="is_active" value="true" {{$data->is_active =='true' ? 'checked' : ''}}>Active</label>
                                        <label class="radio-inline mr-3">
                                            <input type="radio" name="is_active" value="false" {{$data->is_active =='false' ? 'checked' : ''}}>Inactive</label>
                                    @endif
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
        $(document).ready(function(){
            $('#name').keyup(function(e){
                    $('#slug').val($(this).val()
                        .replace(/\s+/g, '-')           // Replace spaces with -
                        .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                        .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                        .toLowerCase()
                    );
            })
            $('#phone').keyup(function () { 
                this.value = this.value.replace(/[^0-9\.]/g,'');
            });
        })
    </script>
@endpush