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
                                <div class="col-lg-12 ml-auto text-right">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="name">Option Type Name <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter option name" value="{{old('name') ? old('name') : $data->name}}" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="is_active">Active</label>
                                <div class="col-lg-6">
                                    <label class="radio-inline mr-3">
                                        <input type="radio" name="is_active" value="true" {{ old('is_active') ? (old('is_active') == 'true' ? 'checked' : '') : ($data->is_active ? 'checked' : '' ) }}>Active</label>
                                    <label class="radio-inline mr-3">
                                        <input type="radio" name="is_active" value="false" {{ old('is_active') ? (old('is_active') == 'false' ? 'checked' : '') : (!$data->is_active ? 'checked' : '' ) }}>Inactive</label>
                                </div>
                            </div>
                            <hr>
                            <div class="option-container">
                                @foreach($data->option as $opt)
                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <input type="hidden" class="option-id" value="{{$opt->id}}" /> 
                                        <input type="hidden" class="option-type-id" value="{{$opt->option_type_id}}" /> 
                                        <input type="text" class="form-control option-name" placeholder="Enter options" value="{{$opt->name}}" required />
                                    </div>
                                    <div class="col-lg-2 btn-container">
                                        <button type="button" class="btn btn-info btn-edit"><i class="fa fa-pencil"></i></button>
                                        <button type="button" class="btn btn-success btn-save-edit d-none"><i class="fa fa-check"></i></button>
                                        <button type="button" class="btn btn-danger btn-cancel-edit d-none"><i class="fa fa-close"></i></button>
                                        <span class="text-success text-success-edit d-none">Successfully Updated</span>
                                        <span class="text-danger text-fail-edit d-none">Update Failed</span>
                                    </div>
                                </div>
                                @endforeach
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
    <script src="{{asset('plugins/validation/jquery.validate.min.js')}}"></script>
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

            $(document).on('click','.btn-cancel-edit',function(){
                $(this).addClass('d-none');
                $(this).closest('.btn-container').find('.btn-save-edit').addClass('d-none');
                $(this).closest('.btn-container').find('.btn-edit').removeClass('d-none');
            })

            $(document).on('click','.btn-edit',function(){
                $('.text-success-edit').addClass('d-none');
                $('.btn-save-edit').addClass('d-none');
                $('.btn-cancel-edit').addClass('d-none');
                $('.btn-edit').removeClass('d-none');
                
                $(this).addClass('d-none');
                $(this).closest('.btn-container').find('.btn-save-edit').removeClass('d-none');
                $(this).closest('.btn-container').find('.btn-cancel-edit').removeClass('d-none');
            })

            $(document).on('click','.btn-save-edit',function(){
                var btn = $(this);
                var id = $(this).closest('.option-container').find('.option-id').val();
                var name = $(this).closest('.option-container').find('.option-name').val();
                var option_type_id = $(this).closest('.option-container').find('.option-type-id').val();
                var url = "{{URL::to('/')}}";
                url = url+"/option/update-option/"+id;
                $.ajax({
                    url : url,
                    type: "PUT",
                    data : {
                        name: name,
                        option_type_id:option_type_id,
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(data, textStatus, jqXHR)
                    {
                        var result = JSON.parse(data);
                        if(result.is_ok){

                            btn.closest('.btn-container').find('.btn-save-edit').addClass('d-none');
                            btn.closest('.btn-container').find('.btn-cancel-edit').addClass('d-none');
                            btn.closest('.btn-container').find('.btn-edit').removeClass('d-none');

                            btn.closest('.btn-container').find('.text-success-edit').text(result.message);
                            btn.closest('.btn-container').find('.text-success-edit').removeClass('d-none');
                        }
                        //data - response from server
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                
                    }
                });
                console.log(id,name);
            })
        });
    </script>
@endpush