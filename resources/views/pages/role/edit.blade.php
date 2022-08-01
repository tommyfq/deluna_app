@push('styles')
<style>
.nav-pills .nav-link.active, .nav-pills .show > .nav-link {
    color: #fff;
    background-color: #7571f9;
}
a {
    transition: all 0.4s ease-in-out;
    color: #76838f;
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
                        <form class="form-valide" action="{{route($_page.'.update', [$data->id])}}" method="post">
                            @method('put')
                            @csrf
                            <div class="form-group row">
                                <div class="col-lg-12 ml-auto text-right">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="name">Role Name <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="name" name="name" value="{{old('name') ? old('name') : ($data->role_name ? $data->role_name : '')}}" placeholder="Enter your product name" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="is_active">Active <span class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <label class="radio-inline mr-3">
                                        <input type="radio" name="is_active" value="true" {{old('is_active') == 'true' ? 'checked' : ($data->is_active ? 'checked' : '')}}>Active</label>
                                    <label class="radio-inline mr-3">
                                        <input type="radio" name="is_active" value="false" {{old('is_active') == 'false' ? 'checked' : (!$data->is_active ? 'checked' : '')}}>Inactive</label>
                                </div>
                            </div>
                            <hr>
                            <div class="options-div">
                                <div class="container-opt">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 col-lg-3 overflow-auto border-right" style="height: 300px;">
                                            <div class="nav flex-column nav-pills">
                                                @foreach($_menu as $val)
                                                <a href="#v-pills-{{$val->menu_id}}" data-toggle="pill" class="nav-link">{{$val->menu_name}}</a>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-7" style="height: 300px; margin-left: 30px;">
                                            <div class="tab-content">
                                                @foreach($_menu as $val)
                                                <div id="v-pills-{{$val->menu_id}}" class="tab-pane fade">
                                                    @if(sizeof($val->action)>0)
                                                        @foreach($val->action as $cval)
                                                        <div class="row">
                                                            <label class=" col-form-label col-lg-2">
                                                                <input type="checkbox" class="form-check-input" name="menu[{{$val->menu_id}}][]" value="{{$cval->action_id}}" {{$cval->checked ? 'checked' : ''}}>
                                                                {{$cval->action_name}}
                                                            </label>
                                                        </div>
                                                        @endforeach
                                                    @else
                                                        <div class="row">
                                                            <b><i>there are no enabled actions for menu <u class="text-primary">{{$val->menu_name}}</u></i></b>
                                                        </div>
                                                    @endif
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
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
        });
    </script>
@endpush