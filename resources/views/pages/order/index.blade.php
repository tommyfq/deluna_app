@push('styles')
<link rel="stylesheet" href="{{asset('plugins/tables/css/datatable/dataTables.bootstrap4.min.css')}}">
@endpush
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @include('_includes.alert')
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">{{ucwords($_page)}} Menu</h4>
                            <p class="text-muted m-b-15 f-s-12">You can see the list of categories, add, edit and delete {{ucwords($_page)}}.</p>
                        </div>
                        <div class="col">
                            <a href="{{route($_page.'.add')}}" class="btn mb-1 btn-primary float-right">Create {{ucwords($_page)}}</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered verticle-middle yajra-datatable">
                                    <thead>
                                        <tr>
                                            <th scope="col">Order Number</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Total price</th>
                                            <th scope="col">Discount</th>
                                            <th scope="col">Sales Channel</th>
                                            <th scope="col">Customer Name</th>
                                            <th scope="col">Customer Phone</th>
                                            <th scope="col">Customer Address</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')

<script src="{{asset('plugins/jqueury/jquery.min.js')}}"></script>
<script src="{{asset('plugins/tables/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/tables/js/datatable/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/tables/js/datatable-init/datatable-basic.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        var table = $('.yajra-datatable').DataTable({
          processing: true,
          serverSide: true,
          order:[[0,'desc']],
          ajax: "{{ route($_page.'.list') }}",
          columns: [
            {data: 'order_no', name: 'order_no'},
            {data: 'status', name: 'status'},
            {data: 'total_price', name: 'total_price'},
            {data: 'discount', name: 'discount'},
            {data: 'sales_channel', name: 'sales_channel'},
            {data: 'customer_name', name: 'customer_name'},
            {data: 'customer_phone', name: 'customer_phone'},
            {data: 'address', name: 'address'}
          ]
      });
    })
</script>
@endpush