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
                                <label class="col-lg-4 col-form-label" for="name">Product Name <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your product name" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="description">Product Description
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="description" name="description" placeholder="Enter your product description" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="category">Category <span class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <select class="form-control" id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        @if($_category)
                                            @foreach($_category as $val)
                                                <option value="{{$val->id}}">{{$val->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            @for($i=0; $i<2; $i++)
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="type">Select Type {{$i+1}} <span class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <select class="form-control type" id="type_{{$i}}" name="type_{{$i}}" required>
                                        <option value="">Select Data</option>
                                        @if($_opt_type)
                                            @foreach($_opt_type as $val)
                                                <option value="{{$val->id}}">{{ucwords($val->name)}}</option>
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
                            <div class="options-div" style="display: none;">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label" id="lbl_type_0" for="type"></label>
                                    <label class="col-lg-3 col-form-label" id="lbl_type_1" for="type"></label>
                                    <label class="col-lg-3 col-form-label" for="stock"> Stock </label>
                                </div>
                                <div class="container-opt">
                                    <div class="form-group row">
                                        <div class="col-lg-3">
                                            <select class="form-control option-0" name="option_0[]" required>
                                                <option value="">Select Data</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-3">
                                            <select class="form-control option-1" name="option_1[]" required>
                                                <option value="">Select Data</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-3">
                                            <input type="number" min=0 class="form-control option-name" name="stocks[]" placeholder="Enter Stock" required>
                                        </div>
                                    </div>
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
    <script>
        $(document).ready(function(){
            var opt_list = [];
            $(document).find("select[id^='type_']").on('change', async function(){
                let value = $('#'+this.id).find(":selected").val();
                let num = this.id.split('_')[1];
                let txt = $('#'+this.id).find(":selected").text();
                if(!value){
                    $('#lbl_type_'+num).text('');
                    return false;
                }
                $('#lbl_type_'+num).text(txt);
                // get data
                await get_data(value, num);
                let lbl1 = $('#lbl_type_0').text();
                let lbl2 = $('#lbl_type_1').text();
                if(lbl1 && lbl2){
                    if(lbl1 == lbl2){
                        toastr.error(
                            'Type has same value!',
                            "Top Right",
                        )
                        $('.options-div').hide();
                    } else {
                        $('.options-div').show();
                        // remove and append
                        $('.option-0 option, .option-1 option').remove();
                        for(let i=0; i<opt_list.length; i++){
                            $('.option-'+i).append(opt_list[i].idx);
                        }
                    }
                } else{
                    $('.options-div').hide();
                }
            });

            $('#btn-add').click(function(e){
                let html = `<div class="form-group row list-stock">
                                <div class="col-lg-3">
                                    <select class="form-control option-0" name="option_0[]" required>
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <select class="form-control option-1" name="option_1[]" required>
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <input type="number" min=0 class="form-control option-name" name="stocks[]" placeholder="Enter Stock" required>
                                </div>
                                <div class="col-lg-1">
                                    <button type="button" class="btn btn-danger btn-del-option"> <i class="fa fa-trash"></i></button>
                                </div>
                            </div>`;
                $('.container-opt').append(html);
                for(let i=0; i<opt_list.length; i++){
                    $('.option-'+i).append(opt_list[i].idx);
                }
            });
            
            $(document).on('click','.btn-del-option',function(){
                $(this).closest('.list-stock').remove();
            })

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