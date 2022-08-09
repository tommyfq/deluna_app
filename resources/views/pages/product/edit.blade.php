@push('styles')
<link rel="stylesheet" href="{{asset('plugins/toastr/css/toastr.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/sweetalert2/dist/sweetalert2.min.css')}}">
@endpush
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
                                <label class="col-lg-4 col-form-label" for="name">Product Name <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="name" name="name" value="{{old('name') ? old('name') : $data->name}}" placeholder="Enter your product name" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="description">Product Description
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="description" name="description" value="{{old('description') ? old('description') : $data->description}}" placeholder="Enter your product description" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="category">Category <span class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <select class="form-control" id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        @if($_category)
                                            @foreach($_category as $val)
                                                <option value="{{$val->id}}" {{old('category') ? (old('category') == $val->id ? 'selected' : '') : ($data->category_id == $val->id ? 'selected' : '')}}>{{$val->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            @for($i=0; $i<2; $i++)
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="type">Select Type {{$i+1}} <span class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <select class="form-control type" id="type_{{$i}}" name="type_{{$i}}" required disabled>
                                        <option value="">Select Data</option>
                                        @if($_opt_type)
                                            @foreach($_opt_type as $val)
                                                <option value="{{$val->id}}" {{old('type_'.$i) ? (old('type_'.$i) == $val->id ? 'selected' : '') : ($data["type_".($i+1)] == $val->id ? 'selected' : '') }}>{{ucwords($val->name)}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            @endfor
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
                            <div class="options-div">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label" id="lbl_type_0" for="type">{{ucwords($data->type_1_name)}}</label>
                                    @if($data->type_2_name)
                                    <label class="col-lg-2 col-form-label" id="lbl_type_1" for="type">{{ucwords($data->type_2_name)}}</label>
                                    @endif
                                    <label class="col-lg-2 col-form-label" for="price"> Price </label>
                                    <label class="col-lg-2 col-form-label" for="sales_price"> Sales Price </label>
                                    <label class="col-lg-2 col-form-label" for="stock"> Stock </label>
                                </div>
                                <div class="container-opt">
                                    @for($i=0; $i<count($_stock); $i++)
                                    <div class="form-group row option-row" id="row_{{$_stock[$i]->id}}" data-id="{{$_stock[$i]->id}}">
                                        <div class="col-lg-2">
                                            <select class="form-control option-0" required>
                                                <option value="">Select Data</option>
                                                @foreach($_option_1 as $val)
                                                <option value="{{$val->id}}" {{($_stock[$i]->option_1 == $val->id ? 'selected data-name='.$val->name : '')}}>{{$val->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if(count($_option_2) > 0)
                                        <div class="col-lg-2">
                                            <select class="form-control option-1">
                                                <option value="">Select Data</option>
                                                @foreach($_option_2 as $val)
                                                <option value="{{$val->id}}" {{($_stock[$i]->option_2 == $val->id ? 'selected data-name='.$val->name : '')}}>{{$val->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @endif
                                        <div class="col-lg-2">
                                            <input type="number" min=0 class="form-control option-price" placeholder="Enter Price" value="{{$_stock[$i]->price}}" {{strtolower($_auth->role_name) == 'super admin' ? 'required' : 'readonly'}}>
                                        </div>
                                        <div class="col-lg-2">
                                            <input type="number" min=0 class="form-control option-sales-price" placeholder="Enter Sales Price" value="{{$_stock[$i]->sales_price}}" {{strtolower($_auth->role_name) == 'super admin' ? 'required' : 'readonly'}}>
                                        </div>
                                        <div class="col-lg-2">
                                            <input type="number" min=0 class="form-control option-stock" placeholder="Enter Stock" value="{{$_stock[$i]->stock}}" required>
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
                                    @endfor
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <button id="btn-add" type="button" class="btn btn-info btn-block">+ Add Option</button>
                                    </div>
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
    <script src="{{asset('plugins/toastr/js/toastr.min.js')}}"></script>
    <script src="{{asset('plugins/sweetalert2/dist/sweetalert2.min.js')}}"></script>
    <script>
        $(document).ready(async function(){
            var opt_list = [];
            $('#btn-add').click(async function(e){
                let lbl1 = $('#lbl_type_0').text();
                let lbl2 = $('#lbl_type_1').text();
                await get_data("{{$data["type_1"]}}", 0);
                await get_data("{{$data["type_2"]}}", 1);
                let html = `<div class="form-group row list-stock">
                                <div class="col-lg-2">
                                    <select class="form-control option-0" name="option_0[]" required>
                                    </select>
                                </div>`;
                if(lbl2 && lbl2 !== 'Select Data'){
                    html+= `<div class="col-lg-2">
                                <select class="form-control option-1" name="option_1[]">
                                </select>
                            </div>`
                }
                html+= `<div class="col-lg-2">
                                    <input type="number" min=0 class="form-control option-price" name="price[]" placeholder="Enter Price" required>
                                </div>
                                <div class="col-lg-2">
                                    <input type="number" min=0 class="form-control option-sales-price" name="sales_price[]" placeholder="Enter Sales Price" required>
                                </div>
                                <div class="col-lg-2">
                                    <input type="number" min=0 class="form-control option-stock" name="stocks[]" placeholder="Enter Stock" required>
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-danger btn-del-option"> <i class="fa fa-trash"></i></button>
                                </div>
                            </div>`;
                $('.container-opt').append(html);
                for(let i=0; i<opt_list.length; i++){
                    $('.option-'+i).append(opt_list[i].idx);
                }
            });
            
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
            $(document).on('click','.btn-del-option',function(){
                $(this).closest('.list-stock').remove();
            });
            $(document).on('click','.btn-save-edit',function(){
                var btn = $(this);
                var id = $(this).closest('.option-row').data('id');
                var opt_0 = $(this).closest('.option-row').find('.option-0').val();
                var opt_1 = $(this).closest('.option-row').find('.option-1').val();
                var stock = $(this).closest('.option-row').find('.option-stock').val();
                var url = "{{URL::to('/')}}";
                url = url+"/product/update-stock/"+id;
                $.ajax({
                    url : url,
                    type: "PUT",
                    data : {
                        opt_0: opt_0,
                        opt_1: opt_1,
                        stock: stock,
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
                        toastr.success(textStatus);
                    }
                });
            });
            $(document).on('click','.btn-delete-option',function(){
                
                var id = $(this).closest('.option-row').data('id');
                var opt_0 = $(this).closest('.option-row').find('.option-0 :selected').data('name');
                var opt_1 = $(this).closest('.option-row').find('.option-1 :selected').data('name');
                var stock = $(this).closest('.option-row').find('.option-stock').val();
                Swal.fire({
                    title: 'Are you sure want to delete this stock '+opt_0+', '+opt_1+', qty '+stock+' ?',
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                        if (result.value) {
                            var url = "{{URL::to('/')}}/product/delete-stock/"+id;
                            $.ajax({
                                url : url,
                                type: "DELETE",
                                data:{
                                    _token: "{{ csrf_token() }}",
                                },
                                success: function(data, textStatus, jqXHR)
                                {
                                    var result = JSON.parse(data);
                                    if(result.is_ok){
                                        $('#row_'+id).remove();
                                        toastr.success(result.message);
                                    }else{
                                        toastr.error(result.message);
                                    }
                                    //data - response from server
                                },
                                error: function (jqXHR, textStatus, errorThrown)
                                {
                            
                                }
                            });
                        }
                })
            });
            async function get_data(id, idx){
                let url = "{{route('product.getoptions')}}";
                await $.ajax({
                    url: url,
                    type: 'post',
                    data: {
                        id: id,
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(data, textStatus, jqXHR)
                    {
                        var result = JSON.parse(data);
                        if(result.is_ok){
                            var html = '<option> Select Data </option>';
                            $.each(result.data, function(idx, val){
                                html += '<option value="'+val.id+'">'+val.name+'</option>';
                            });
                            opt_list.push({idx:html});
                        } else {
                            delete opt_list[idx]
                            toastr.error(
                                result.message,
                                "Top Right",
                                {
                                    positionClass:"toast-top-right",
                                    timeOut:5e3,
                                    closeButton:!0,
                                    debug:!1,
                                    newestOnTop:!0,
                                    progressBar:!0,
                                    preventDuplicates:!0,
                                    onclick:null,
                                    showDuration:"300",
                                    hideDuration:"1000",
                                    extendedTimeOut:"1000",
                                    showEasing:"swing",
                                    hideEasing:"linear",
                                    showMethod:"fadeIn",
                                    hideMethod:"fadeOut",
                                    tapToDismiss:!1
                                })
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        toastr.error(
                            textStatus,
                            "Top Right",
                            {
                                positionClass:"toast-top-right",
                                timeOut:5e3,
                                closeButton:!0,
                                debug:!1,
                                newestOnTop:!0,
                                progressBar:!0,
                                preventDuplicates:!0,
                                onclick:null,
                                showDuration:"300",
                                hideDuration:"1000",
                                extendedTimeOut:"1000",
                                showEasing:"swing",
                                hideEasing:"linear",
                                showMethod:"fadeIn",
                                hideMethod:"fadeOut",
                                tapToDismiss:!1
                            })
                    }
                });
            }
        });
    </script>
@endpush