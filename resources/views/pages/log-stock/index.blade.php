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
                            <h4 class="card-title">{{ucwords(str_contains($_page, '-') ? str_replace('-', ' ', $_page) : $_page)}} Menu</h4>
                            <p class="text-muted m-b-15 f-s-12">You can see the log of stocks here.</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered verticle-middle yajra-datatable">
                                    <thead>
                                        <tr>
                                            <th scope="col">Reference</th>
                                            <th scope="col">Type</th>
                                            <th scope="col">Stock From</th>
                                            <th scope="col">Stock To</th>
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
          ajax: "{{ route($_page.'.list') }}",
          columns: [
              {data: 'reference', name: 'reference'},
              {data: 'type', name: 'type'},
              {data: 'stock_from', name: 'stock_from'},
              {data: 'stock_to', name: 'stock_to'},
          ],
          order: [
            [0, 'desc']
          ]
      });
    })
</script>
@endpush