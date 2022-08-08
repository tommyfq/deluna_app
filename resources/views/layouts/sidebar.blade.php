<div class="nk-sidebar">
    <div class="nk-nav-scroll">
        <ul class="metismenu" id="menu">
            @if($_sidebar)
                @foreach($_sidebar as $val)
                    <li>
                        @if($val->submenu && count($val->submenu)>0)
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <i class="{{$val->icon}} menu-icon"></i><span class="nav-text">{{$val->menu}}</span>
                        </a>
                        @foreach($val->submenu as $cval)
                        <ul aria-expanded="false">
                            <li><a href="{{url($cval->slug)}}">{{$cval->menu}}</a></li>
                        </ul>
                        @endforeach
                        @else
                        <a href="{{url($val->slug)}}" aria-expanded="false">
                            <i class="{{$val->icon}} menu-icon"></i><span class="nav-text">{{$val->menu}}</span>
                        </a>
                        @endif
                    </li>
                @endforeach
            @endif
        </ul>
    </div>
</div>