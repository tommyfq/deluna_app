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
                            <h4 class="card-title">{{ucwords($_page)}}s Menu</h4>
                            <p class="text-muted m-b-15 f-s-12">You can see the list of {{$_page}}s, add, edit and delete {{ucwords($_page)}}.</p>
                        </div>
                        <div class="col">
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
                                            <th scope="col">Name</th>
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
                <a id="modal-save" href="#"><button type="button" class="btn btn-primary">Save changes</button></a>
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
              {data: 'name', name: 'name'},
              {data: 'is_active', name: 'status'},
              {
                  data: 'action', 
                  name: 'action', 
                  orderable: false, 
                  searchable: false
              },
          ]
      });

      $(document).on("click",".btn-delete",function(e) {
        e.preventDefault();
        var name = $(this).data('name');
        var url = $(this).attr('href');
        $('#modal-title').text('Are you sure want to delete '+name+' ?');
        $('#modal-save').attr('href',url);
        $('#basicModal').modal('show');
      });
    })
</script>
@endpush