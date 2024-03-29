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
                        <div class="col-12 col-md-6">
                            <h4 class="card-title">{{ucwords($_page)}} Menu</h4>
                            <p class="text-muted m-b-15 f-s-12">You can see the list of categories, add, edit and delete {{ucwords($_page)}}.</p>
                        </div>
                        <div class="col-12 col-md-6">
                            @if($_role->add)
                            <a href="{{route($_page.'.add')}}" class="btn mb-1 btn-primary float-right">Add {{ucwords($_page)}}</a>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered verticle-middle yajra-datatable">
                                    <thead>
                                        <tr>
                                            <th scope="col">Role Name</th>
                                            <th scope="col">Active</th>
                                            <th scope="col">Action</th>
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

<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
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
              {data: 'role_name', name: 'role_name'},
              {data: 'is_active', name: 'active'},
              {
                  data: 'action', 
                  name: 'action', 
                  orderable: false, 
                  searchable: false
              },
          ]
      });
    })
</script>
@endpush