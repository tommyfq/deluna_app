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
                        <form id="form-order" class="form-valide" action="{{route($_page.'.store')}}" method="post">
                            @csrf
                            <div class="form-group row">
                                <div class="col-lg-12 ml-auto text-right">
                                    <button id="btn-submit" type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Customer Name <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="customer_name" placeholder="Enter your product name" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Customer Phone <span class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="customer_phone" placeholder="Enter your product description" value="{{old('customer_phone')}}" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Customer Email <span class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <input type="email" class="form-control" name="customer_email" placeholder="Enter your product description" value="{{old('customer_email')}}" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Address <span class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <textarea class="form-control" id="address" placeholder="Enter Address" name="address" required>{{old('address')}}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Sales Channel <span class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <select class="form-control" name="sales_channel_id" required>
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
                                <label class="col-lg-4 col-form-label">Sales Channel Notes</label>
                                <div class="col-lg-6">
                                    <textarea class="form-control" name="sales_channel_notes" placeholder="Enter Sales Channel Notes">{{old('sales_channel_notes')}}</textarea>
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
                                            <th style="width:10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="product-detail-container">
                                        <tr class="product-detail-row">
                                            <td>
                                                <select style="width:100%" class="form-control product-select2" name="products[]" required>
                                                    <option value="">Please Select Product</option>
                                                    @foreach($_products as $prod)
                                                        <option value="{{$prod->id}}">{{$prod->name}}</option>
                                                    @endforeach
                                                </select>
                                            </td> 
                                            <td class="option-container">
                                                Please Select the Product First
                                            </td>
                                            <td class="container-product-stock">
                                                <input type="number" name="quantity[]" class="form-control product-detail-qty" required min="1" value="1"/>
                                                <input type="hidden" name="stock_id[]" class="hidden-stock-id" />
                                                <label class="product-detall-stock-info">Stock : </label>
                                                <label class="text-danger alert-stock"></label>
                                            </td>
                                            <td class="product-detail-price">
                                                -
                                            </td>
                                            <td class="product-detail-subtotal">
                                                -
                                            </td>
                                            <td>
                                                
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td class="tfoot-info">
                                                <button id="btn-add" type="button" class="btn btn-info btn-block">+ Add Product</button>
                                            </td>
                                        </tr>
                                        <tr >
                                            <td colspan="4">Discount</td>
                                            <td><input id="order-discount" type="number" class="form-control" name="discount" value="0" /></td>
                                        </tr>
                                        <tr>
                                            <td class="tfoot-info"colspan="4">Total</td>
                                            <td class="tfoot-info order-total">-</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                        </form>
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

            $('#btn-add').on('click',function(){
                productCount++;
                $('.product-detail-container').append(`
                <tr class="product-detail-row">
                    <td>
                        <select style="width:100%" class="form-control product-select2" name="products[]" required>
                            <option value="">Please Select Product</option>
                            @foreach($_products as $prod)
                                <option value="{{$prod->id}}">{{$prod->name}}</option>
                            @endforeach
                        </select>
                    </td> 
                    <td class="option-container">
                        Please Select the Product First
                    </td>
                    <td class="container-product-stock">
                        <input type="number" name="quantity[]" class="form-control product-detail-qty" required min="1" value="1"/>
                        <input type="hidden" name="stock_id[]" class="hidden-stock-id" />
                        <label class="product-detall-stock-info">Stock : </label>
                        <label class="text-danger alert-stock"></label>
                    </td>
                    <td class="product-detail-price">
                        -
                    </td>
                    <td class="product-detail-subtotal">
                        -
                    </td>
                    <td><button type="button" class="btn btn-danger btn-del-option"> <i class="fa fa-trash"></i></button></td>
                </tr>
                `
                );
                $('.product-select2').select2();
            });

            $(document).on('click','#btn-submit',function(e){
                let isFormValid = $('#form-order')[0].checkValidity();
                if(!isFormValid){
                    $('#form-order')[0].reportValidity();
                }else{
                    e.preventDefault();
                }

                var form = $('#form-order').serializeArray();

                var url = "{{URL::to('/')}}";
                    url = url+"/order";
                    $.ajax({
                        url : url,
                        type: "POST",
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
                                    allowOutsideClick: false
                                }).then((result) => {
                                    if (result.value) {
                                        window.location = url;
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