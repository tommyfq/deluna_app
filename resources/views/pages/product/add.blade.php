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
                                    <input type="text" class="form-control" id="name" name="name" value="{{old('name')}}" placeholder="Enter your product name" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="description">Product Description
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="description" name="description" value="{{old('description')}}" placeholder="Enter your product description" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="category">Category <span class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <select class="form-control" id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        @if($_category)
                                            @foreach($_category as $val)
                                                <option value="{{$val->id}}" {{old('category') == $val->id ? 'selected' : ''}}>{{$val->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            @for($i=0; $i<2; $i++)
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="type">Select Type {{$i+1}} {!!$i%2 != 0 ? '' : '<span class="text-danger">*</span>'!!}</label>
                                <div class="col-lg-6">
                                    <select class="form-control type" id="type_{{$i}}" name="type_{{$i}}" {{$i%2 != 0 ? '' : 'required'}}>
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
                                    <label class="col-lg-2 col-form-label" id="lbl_type_0" for="type" style="display: none;"></label>
                                    <label class="col-lg-2 col-form-label" id="lbl_type_1" for="type" style="display: none;"></label>
                                    <label class="col-lg-2 col-form-label" for="price"> Price </label>
                                    <label class="col-lg-2 col-form-label" for="sales_price"> Sales Price </label>
                                    <label class="col-lg-2 col-form-label" for="stock"> Stock </label>
                                </div>
                                <div class="container-opt">
                                    <div class="form-group row">
                                        <div class="col-lg-2 opt-0" style="display: none">
                                            <select class="form-control option-0" name="option_0[]" required>
                                                <option value="">Select Data</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-2 opt-1" style="display: none">
                                            <select class="form-control option-1" name="option_1[]">
                                                <option value="">Select Data</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-2">
                                            <input type="number" min=0 class="form-control option-name" name="price[]" placeholder="Enter Price" required>
                                        </div>
                                        <div class="col-lg-2">
                                            <input type="number" min=0 class="form-control option-name" name="sales_price[]" placeholder="Enter Sales Price" required>
                                        </div>
                                        <div class="col-lg-2">
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
                $('.options-div').hide();
                $('.list-stock').remove();
                let value = $('#'+this.id).find(":selected").val();
                let num = this.id.split('_')[1];
                let txt = $('#'+this.id).find(":selected").text();
                $('#lbl_type_'+num).text(txt);
                // get data
                await get_data(value, num);
                let lbl1 = $('#lbl_type_0').text();
                let lbl2 = $('#lbl_type_1').text();
                if(lbl1){
                    if(lbl1 == lbl2){
                        toastr.error(
                            'Type has same value!',
                            "Top Right",
                        )
                    } else {
                        $('.options-div').show();
                        $('.option-0 option, .option-1 option').remove();
                        let side = '';
                        if((lbl1 && lbl1 !== 'Select Data') && (lbl2 && lbl2 !== 'Select Data')){
                            side = '';
                            $('#lbl_type_0').css('display','block');
                            $('#lbl_type_1').css('display','block');
                            $('.opt-0').css('display','block');
                            $('.opt-1').css('display','block');
                            $('.option-0').append(opt_list[0]);
                            $('.option-1').append(opt_list[1]);
                        } else if(lbl1 && lbl1 !== 'Select Data'){
                            side = 1;
                            $('#lbl_type_'+num).css('display','block');
                            $('#lbl_type_'+side).css('display','none');
                            $('.opt-'+num).css('display','block');
                            $('.opt-'+side).css('display','none');
                            if(num == 0)
                                $('.option-'+num).append(opt_list[num]);
                            else
                                $('.option-0').append(opt_list[0]);
                        } else if(lbl2 && lbl2 !== 'Select Data'){
                            side = 0;
                            $('#lbl_type_'+num).css('display','block');
                            $('#lbl_type_'+side).css('display','none');
                            $('.opt-'+num).css('display','block');
                            $('.opt-'+side).css('display','none');
                            if(num == 1)
                                $('.option-'+num).append(opt_list[num]);
                        }
                        console.log('side: ', side, 'num: ', num, 'lbl1: ', lbl1, 'lbl2: ', lbl2, opt_list)
                    }
                } else{
                    $('.options-div').hide();
                }
            });

            $('#btn-add').click(function(e){
                let lbl1 = $('#lbl_type_0').text();
                let lbl2 = $('#lbl_type_1').text();
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
                    $('.option-'+i).append(opt_list[i]);
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
                            var html = '<option value=""> Select Data </option>';
                            $.each(result.data, function(idx, val){
                                html += '<option value="'+val.id+'">'+val.name+'</option>';
                            });
                            opt_list[idx] = html
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
                        console.log(opt_list)
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