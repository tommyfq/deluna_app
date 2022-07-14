@if(isset($_breadcrumbs))
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                @php $last_element = end($_breadcrumbs); @endphp
                @foreach($_breadcrumbs as $key => $value)
                    <li class="breadcrumb-item {{$value == $last_element ? 'active' : ''}}"><a href="{{$value == $last_element ? 'javascript:void(0)' : $value}}">{{$key}}</a></li>
                @endforeach
            </ol>
        </div>
    </div>
@endif