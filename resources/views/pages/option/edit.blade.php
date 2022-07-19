@push('styles')
<link rel="stylesheet" href="{{asset('plugins/toastr/css/toastr.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/sweetalert2/dist/sweetalert2.min.css')}}">
@endpush

<div class="container-fluid">
    <div id="message-error" class="alert alert-danger d-none"></div>
    <div id="message-success" class="alert alert-success d-none"></div>
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    @include('_includes.alert')
                    <div class="form-validation">
                        <form id="form_option" class="form-valide" action="{{route($_page.'.update',[$data->id])}}" method="post">
                            @method('put')
                            @csrf
                            <div class="form-group row">
                                <div class="col-lg-12 ml-auto text-right">
                                    <button id="btn-save" type="submit" class="btn btn-primary">Submit</button>
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
                                <div id="row_option_{{$opt->id}}" class="form-group row option-row">
                                    <div class="col-lg-6">
                                        <input type="hidden" class="option-id" value="{{$opt->id}}" /> 
                                        <input type="hidden" class="option-type-id" value="{{$opt->option_type_id}}" /> 
                                        <input type="text" class="form-control option-name" placeholder="Enter options" value="{{$opt->name}}" required />
                                    </div>
                                    <div class="col-lg-2 btn-container">
                                        <button type="button" class="btn btn-info btn-edit"><i class="fa fa-pencil"></i></button>
                                        <button type="button" class="btn btn-success btn-save-edit d-none"><i class="fa fa-check"></i></button>
                                        <button type="button" class="btn btn-danger btn-cancel-edit d-none"><i class="fa fa-close"></i></button>
                                        <button type="button" class="btn btn-danger btn-delete-option"><i class="fa fa-trash"></i></button>
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
<div class="modal fade" id="basicModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modal-title" class="modal-title">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                </button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="modal-delete" type="button" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script src="{{asset('plugins/validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('plugins/toastr/js/toastr.min.js')}}"></script>
    <script src="{{asset('plugins/sweetalert2/dist/sweetalert2.min.js')}}"></script>
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

            $(document).on('click','.btn-delete-option',function(){
                $('#basicModal').modal('show');
                var id = $(this).closest('.option-row').find('.option-id').val();
                var name = $(this).closest('.option-row').find('.option-name').val();
                $('#modal-title').text('Are you sure want to delete '+name+' ?');
                $('#modal-delete').data('id',id);
                $('#modal-delete').data('name',name);
            });

            $(document).on('click','#modal-delete',function(){
                var id = $(this).data('id');
                var name = $(this).data('name');
                var url = "{{URL::to('/')}}/option/delete-option/"+id;
                $.ajax({
                    url : url,
                    type: "DELETE",
                    data:{
                        _token: "{{ csrf_token() }}",
                        name: name
                    },
                    success: function(data, textStatus, jqXHR)
                    {
                        var result = JSON.parse(data);
                        if(result.is_ok){
                            $('#row_option_'+result.option_id).remove();
                            toastr.success(result.message);
                        }else{
                            toastr.error(result.message);
                        }
                        $('#basicModal').modal('hide');
                        //data - response from server
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                
                    }
                });
                console.log(id,name);
            });

            $(document).on('click','.btn-save-edit',function(){
                var btn = $(this);
                var id = $(this).closest('.option-row').find('.option-id').val();
                var name = $(this).closest('.option-row').find('.option-name').val();
                var option_type_id = $(this).closest('.option-row').find('.option-type-id').val();
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
                            toastr.success(result.message);
                        }
                        //data - response from server
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        
                    }
                });
                console.log(id,name);
            })

            $(document).on('click','#btn-save',function(e){
                e.preventDefault();
                var data = $('#form_option').serialize();
                $.ajax({
                    url : "{{route($_page.'.update',[$data->id])}}",
                    type: "PUT",
                    data : data,
                    success: function(data, textStatus, jqXHR)
                    {
                        var result = JSON.parse(data);
                        if(result.is_ok){
                            Swal.fire({
                                icon: 'success',
                                title: result.message,
                                confirmButtonText: 'Close',
                            }).then((result) => {
                                if (result.value) {
                                    location.reload();
                                }
                            })
                        }else{
                            toastr.error(result.message);
                        }
                        //data - response from server
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        
                    }
                });
            })
        });
    </script>
@endpush