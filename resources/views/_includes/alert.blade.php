@if(Session::has('message.success'))
    <div class="alert alert-success">{{Session::get('message.success')}}</div>
@elseif(Session::has('message.error'))
    <div class="alert alert-danger">{{Session::get('message.error')}}</div>
@elseif(Session::has('message.warning'))
    <div class="alert alert-warning">{{Session::get('message.warning')}}</div>
@endif

{{-- validator error --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif