@push('styles')
    <link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/sweetalert2/dist/sweetalert2.min.css')}}">
    <style>
        tfoot .tfoot-info{
            border:none !important;
        }
    </style>
@endpush
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    @include('_includes.alert')
                    <div class="form-validation">
                        <form id="form-order" class="form-valide" action="{{route($_page.'.update',[$_order->id])}}" method="post">
                            @method('put')
                            @csrf
                            <div class="form-group row">
                                <div class="col-lg-3 offset-lg-9 ml-auto text-right d-inline">
                                    @if(count($_status) > 0)
                                    <select class="form-control d-inline" style="width:250px" name="status" required>
                                        @if($_status)
                                            @foreach($_status as $key => $value)
                                                <option value="{{$key}}">{{str_replace("_"," ",strtoupper($key))}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <button id="btn-submit" type="submit" class="btn btn-primary" style="margin-bottom:7px">Submit</button>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Order Number</label>
                                <div class="col-lg-6">
                                    <b>{{str_replace("_"," ",strtoupper($_order->order_no))}}</b>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Order Status</label>
                                <div class="col-lg-6">
                                    {{str_replace("_"," ",strtoupper($_order->status))}}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Customer Name</label>
                                <div class="col-lg-6">
                                    {{$_order->customer_name}}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Customer Phone</label>
                                <div class="col-lg-6">
                                    {{$_order->customer_phone}}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Customer Email</label>
                                <div class="col-lg-6">
                                    {{$_order->customer_email}}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Address</label>
                                <div class="col-lg-6">
                                    {{$_order->address}}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Sales Channel</label>
                                <div class="col-lg-6">
                                    {{$_order->sales->name}}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Notes</label>
                                <div class="col-lg-6">
                                    <textarea class="form-control" name="sales_channel_notes" placeholder="Enter Notes">{{$_order->sales_channel_notes}}</textarea>
                                </div>
                            </div>
                            <hr>
                            <div class="product-container">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th style="width:30%">Product</th>
                                            <th style="width:20%">Option</th>
                                            <th style="width:10%">Qty</th>
                                            <th style="width:15%">Price</th>
                                            <th style="width:15%">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="product-detail-container">
                                        @foreach($_order->order_detail as $detail)
                                        <tr class="product-detail-row">
                                            <td>
                                                {{$detail->product->name}}
                                            </td> 
                                            <td class="option-container">
                                                {{$detail->stock_option->option1->name ?? '-'}}
                                                
                                                @if(isset($detail->stock_option->option2->name))
                                                    <br>
                                                    {{$detail->stock_option->option2->name}}
                                                @endif
                                                
                                            </td>
                                            <td class="container-product-stock">
                                                {{$detail->quantity}}
                                            </td>
                                            <td class="product-detail-price">
                                                {{number_format($detail->price,0,',','.')}}
                                            </td>
                                            <td class="product-detail-subtotal">
                                                {{number_format($detail->quantity*$detail->price,0,',','.')}}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr >
                                            <td colspan="4">Discount</td>
                                            <td>{{number_format($_order->discount,0,',','.')}}</td>
                                        </tr>
                                        <tr>
                                            <td class="tfoot-info"colspan="4">Total</td>
                                            <td class="tfoot-info order-total">{{number_format($_order->grand_total,0,',','.')}}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </form>
                    </div>
                    <div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date time</th>
                                    <th>Status</th>
                                    <th>Changes By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($_order->order_log as $log)
                                <tr>
                                    <td>{{$log->created_at}}</td>
                                    <td>{{$log->status}}</td>
                                    <td>{{$log->user->name}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/sweetalert2/dist/sweetalert2.min.js')}}"></script>

    <script>
        $(document).ready(function(){
            var productCount = 1;
            var total = 0;
            $('.product-select2').select2();

            $(document).on('click','#btn-submit',function(e){
                let isFormValid = $('#form-order')[0].checkValidity();
                if(!isFormValid){
                    $('#form-order')[0].reportValidity();
                }else{
                    e.preventDefault();
                }

                var form = $('#form-order').serializeArray();
                var url = $('#form-order').attr('action');

                    $.ajax({
                        url : url,
                        type: "PUT",
                        data : form,
                        success: function(data, textStatus, jqXHR)
                        {
                            var result = JSON.parse(data);
                            console.log(result.is_ok);
                            if(result.is_ok){
                                Swal.fire({
                                    type: 'success',
                                    title: result.message,
                                    confirmButtonText: 'Close',
                                }).then((result) => {
                                    if (result.value) {
                                        location.reload();
                                    }
                                })
                            }else{
                                console.log(result.hasOwnProperty('data_stock'));
                                if(result.hasOwnProperty('data_stock')){
                                    console.log(result.data_stock);
                                    for(let i = 0; i < result.data_stock.length; i++){
                                        console.log(result.data_stock[i]);
                                        var stockContainer = $('.stock-id-'+result.data_stock[i].stock_id);
                                        stockContainer.find('.alert-stock').text(result.message);

                                        var qtyElement = stockContainer.find('.product-detail-qty');
                                        qtyElement.val(result.data_stock[i].stock_qty);
                                        qtyElement.data('stock-id',result.data_stock[i].stock_id);
                                        qtyElement.data('stock-qty',result.data_stock[i].stock_qty);

                                        var stockInfo = stockContainer.find('.product-detall-stock-info');
                                        stockInfo.text("Stock : "+result.data_stock[i].stock_qty);
                                    }
                                    return;
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Something went wrong!'
                                })

                            }
                            console.log(data);
                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {
                            
                        }
                    });
            })

            $(document).on('click','.btn-del-option',function(){

                $(this).closest('.product-detail-row').remove();
                calculateTotal();
            })

            $(document).on('change', '.product-select2', function(e) {
                // your code
                var productSelect2 = $(this);
                var id = this.value;
                var url = "{{route('order.get-option-list')}}";
                $.ajax({
                    url : url,
                    type: "POST",
                    data : {
                        _token: "{{ csrf_token() }}",
                        product_id: id,
                    },
                    success: function(data, textStatus, jqXHR)
                    {
                        var result = JSON.parse(data);
                        if(result.is_ok){
                            //Delete Stock id in container stock
                            var containerStock = productSelect2.closest('.product-detail-row').find('.container-product-stock');
                            var classContainer = containerStock.attr('class').split(' ');
                            if(classContainer.length > 1){ containerStock.removeClass(classContainer[1]); }

                            //Replace option with product option
                            var optionSelect = productSelect2.closest('.product-detail-row').find('.option-container');
                            optionSelect.empty();
                            optionSelect.append(result.element);
                            $('.option-select2').select2();

                            //Remove Price
                            var priceElement = productSelect2.closest('.product-detail-row').find('.product-detail-price');
                            priceElement.text("-");

                            //Remove Qty
                            var qtyElement = productSelect2.closest('.product-detail-row').find('.product-detail-qty');
                            qtyElement.val(1);
                            qtyElement.data('price',0);
                            qtyElement.removeAttr('stock-id');
                            qtyElement.removeAttr('stock-qty');

                            //Reset Stock in hidden stock id element
                            var stockHiddenElement = productSelect2.closest('.product-detail-row').find('.hidden-stock-id');
                            stockHiddenElement.val("");

                            //Reset label stock info
                            var stockInfo = productSelect2.closest('.product-detail-row').find('.product-detall-stock-info');
                            stockInfo.text("Stock : ");

                            var subTotalElement = productSelect2.closest('.product-detail-row').find('.product-detail-subtotal');
                            updateSubTotal(subTotalElement,0)
                            calculateTotal();
                        }
                        
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        
                    }
                });
            });

            $(document).on('change','.type_1-select',function(e){
                var optionSelect2 = $(this);
                var option_1 = this.value;
                var product_id = $(this).find(":selected").data("product-id");
                var price = $(this).find(":selected").data("price");

                var containerStock = optionSelect2.closest('.product-detail-row').find('.container-product-stock');
                var classContainer = containerStock.attr('class').split(' ');
                if(classContainer.length > 1){ containerStock.removeClass(classContainer[1]); }

                if(price !== undefined){
                    
                    var qtyElement = optionSelect2.closest('.product-detail-row').find('.product-detail-qty');
                    qtyElement.data('price',price);
                    var qty = qtyElement.val();

                    var priceElement = optionSelect2.closest('.product-detail-row').find('.product-detail-price');
                    priceElement.text(parseInt(price).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,"));

                    var subTotalElement = optionSelect2.closest('.product-detail-row').find('.product-detail-subtotal');
                    var subTotal = price*qty;
                    updateSubTotal(subTotalElement,subTotal)
                    calculateTotal();

                    var optionSelect = optionSelect2.closest('.product-detail-row').find('.option-container');

                    optionSelect.append(`
                        <select style="width:100%" class="d-none" name="option_type_2[]">
                            <option value=""></option>
                        </select>
                    `);

                    var url = "{{route('order.get-stock')}}";
                    $.ajax({
                        url : url,
                        type: "POST",
                        data : {
                            _token: "{{ csrf_token() }}",
                            product_id: product_id,
                            option_1:option_1,
                            option_2:null

                        },
                        success: function(data, textStatus, jqXHR)
                        {
                            var result = JSON.parse(data);
                            if(result.is_ok){
                                var qtyElement = optionSelect2.closest('.product-detail-row').find('.product-detail-qty');
                                qtyElement.data('stock-id',result.data.id);
                                qtyElement.data('stock-qty',result.data.stock);

                                var stockHiddenElement = optionSelect2.closest('.product-detail-row').find('.hidden-stock-id');
                                stockHiddenElement.val(result.data.id);

                                var stockInfo = optionSelect2.closest('.product-detail-row').find('.product-detall-stock-info');
                                stockInfo.text("Stock : "+result.data.stock);
                                containerStock.addClass("stock-id-"+result.data.id);
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {
                            
                        }
                    });
                    
                }else{
                    var url = "{{route('order.get-option-list')}}";
                    $.ajax({
                        url : url,
                        type: "POST",
                        data : {
                            _token: "{{ csrf_token() }}",
                            product_id: product_id,
                            option_1:option_1

                        },
                        success: function(data, textStatus, jqXHR)
                        {
                            var result = JSON.parse(data);
                            if(result.is_ok){
                                
                                var optionSelect = optionSelect2.closest('.product-detail-row').find('.option-container');
                                var option2Element = optionSelect2.closest('.product-detail-row').find('.type_2-select');
                                option2Element.remove();
                                option2Element.select2('destroy');
                                optionSelect.append(result.element);
                                $('.option-select2').select2();

                                //Remove Price
                                var priceElement = optionSelect.closest('.product-detail-row').find('.product-detail-price');
                                priceElement.text("-");

                                //Remove Qty
                                var qtyElement = optionSelect.closest('.product-detail-row').find('.product-detail-qty');
                                qtyElement.val(1);
                                qtyElement.data('price',0);
                                qtyElement.removeAttr('stock-id');
                                qtyElement.removeAttr('stock-qty');

                                //Reset Stock in hidden stock id element
                                var stockHiddenElement = optionSelect.closest('.product-detail-row').find('.hidden-stock-id');
                                stockHiddenElement.val("");

                                //Reset label stock info
                                var stockInfo = optionSelect.closest('.product-detail-row').find('.product-detall-stock-info');
                                stockInfo.text("Stock : ");

                                var subTotalElement = optionSelect2.closest('.product-detail-row').find('.product-detail-subtotal');
                                updateSubTotal(subTotalElement,0)
                                calculateTotal();
                            }
                            
                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {
                            
                        }
                    });
                }
                
            });

            $(document).on('change','.type_2-select',function(e){
                var product_id = $(this).find(":selected").data("product-id");
                var option_2 = this.value;

                var price = $(this).find(":selected").data("price");
                var option_1 = $(this).find(":selected").data("option-1");
                
                var optionSelect2 = $(this);

                var qtyElement = optionSelect2.closest('.product-detail-row').find('.product-detail-qty');
                qtyElement.data('price',price);
                var qty = qtyElement.val();

                var priceElement = optionSelect2.closest('.product-detail-row').find('.product-detail-price');
                priceElement.text(parseInt(price).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,"));

                var subTotalElement = optionSelect2.closest('.product-detail-row').find('.product-detail-subtotal');
                var subTotal = price*qty;

                var option1Element = $(this).find(":selected").data("product-id");

                var containerStock = optionSelect2.closest('.product-detail-row').find('.container-product-stock');
                var classContainer = containerStock.attr('class').split(' ');
                if(classContainer.length > 1){ containerStock.removeClass(classContainer[1]); }

                updateSubTotal(subTotalElement,subTotal)
                calculateTotal();

                var url = "{{route('order.get-stock')}}";
                $.ajax({
                    url : url,
                    type: "POST",
                    data : {
                        _token: "{{ csrf_token() }}",
                        product_id: product_id,
                        option_1:option_1,
                        option_2:option_2

                    },
                    success: function(data, textStatus, jqXHR)
                    {
                        var result = JSON.parse(data);
                        if(result.is_ok){
                            console.log(result.data);
                            qtyElement.data('stock-id',result.data.id);
                            qtyElement.data('stock-qty',result.data.stock);
                            console.log(qtyElement);
                            var stockInfo = optionSelect2.closest('.product-detail-row').find('.product-detall-stock-info');
                            stockInfo.text("Stock : "+result.data.stock);

                            var stockHiddenElement = optionSelect2.closest('.product-detail-row').find('.hidden-stock-id');
                            stockHiddenElement.val(result.data.id);

                            containerStock.addClass("stock-id-"+result.data.id);
                            console.log(containerStock.attr('class'));

                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        
                    }
                });
            })

            $(document).on('keyup','.product-detail-qty',function(e){
                var inputQty = $(this)
                var inp = inputQty.val();
                var subTotalElement = inputQty.closest('.product-detail-row').find('.product-detail-subtotal');
                var price = $(this).data('price');
                console.log($(this).data('stock-id'));
                console.log($(this).data('stock-qty'));
                var stockQty = $(this).data('stock-qty');
                if( $(this).val().length !== 0 ) {
                    if(parseInt($(this).val()) < 0){
                        $(this).val(1)
                    }
                }else{
                    $(this).val(1)
                }

                var qty = $(this).val();
                var alertStock = inputQty.closest('.product-detail-row').find('.alert-stock');
                if(qty > stockQty){
                    alertStock.text('Not enough stock');
                    $(this).val(1);
                    return;
                }else{
                    alertStock.text('');
                    var subTotal = qty*price
                    updateSubTotal(subTotalElement,subTotal)
                    calculateTotal();
                }

                // if( $(this).val().length === 0 ) {
                //     $(this).val(0)
                // }else{

                // }
                // if(inp.length == 0){
                //     priceElement.text(0);
                //     updateSubTotal(subTotalElement,0)
                //     calculateTotal();
                //     return;
                // }
                // if(!isNaN(inp)){
                //     if(inp >= 0 && price !== undefined){
                //         var price = $(this).data('price');
                //         var subTotal = inp*price;
                //         updateSubTotal(subTotalElement,subTotal)
                //         calculateTotal();
                //     }else{
                //         priceElement.text(0);
                //         updateSubTotal(subTotalElement,0)
                //         calculateTotal();
                //     }
                // }else{
                //     priceElement.text(0);
                //     updateSubTotal(subTotalElement,0)
                //     calculateTotal();
                // }
            })

            $(document).on("change","#order-discount",function(e){
                if( $(this).val().length !== 0 ) {
                    if(parseInt($(this).val()) < 0){
                        $(this).val(0)
                    }
                }else{
                    $(this).val(0)
                }
                calculateTotal();
            })

            function updateSubTotal(subTotalElement,subTotal){
                console.log(subTotal);
                subTotalElement.data('subtotal',subTotal);
                if(subTotal == 0) subTotalElement.text("-");
                else subTotalElement.text(parseInt(subTotal).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,"));
            }

            function calculateTotal(){
                total = 0
                $('.product-detail-subtotal').each(function(){
                    var sub = $(this).data('subtotal');
                    total += parseInt(sub);
                });

                var discount = parseInt($('#order-discount').val());
                total -= discount;

                $('.order-total').text(parseInt(total).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,"));
            }
        });
    </script>
@endpush