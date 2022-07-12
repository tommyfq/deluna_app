<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @include('_includes.alert')
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">User Menu</h4>
                            <p class="text-muted m-b-15 f-s-12">You can see the list of users, add, edit and delete User.</p>
                        </div>
                        <div class="col">
                            <a href="{{route('user.add')}}" class="btn mb-1 btn-primary float-right">Add User</a>
                        </div>
                    </div>
                    <div class="row">
                        {{-- list --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>