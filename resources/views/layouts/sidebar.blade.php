<div class="nk-sidebar">
    <div class="nk-nav-scroll">
        <ul class="metismenu" id="menu">
            {{-- simple line icon --}}
            <li class="nav-label">Dashboard</li>
            <li>
                <a href="{{route('dashboard.index')}}" aria-expanded="false">
                    <i class="icon-speedometer menu-icon"></i><span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li class="nav-label">Master Data</li>
            <li>
                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="icon-docs menu-icon"></i><span class="nav-text">Master data</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{route('user.index')}}">User</a></li>
                </ul>
                <ul aria-expanded="false">
                    <li><a href="{{route('vendor.index')}}">Vendor</a></li>
                </ul>
                <ul aria-expanded="false">
                    <li><a href="{{route('category.index')}}">Category</a></li>
                </ul>
                <ul aria-expanded="false">
                    <li><a href="{{route('option.index')}}">Option</a></li>
                </ul>
                <ul aria-expanded="false">
                    <li><a href="{{route('product.index')}}">Product</a></li>
                </ul>
            </li>
            <li>
                <a href="{{route('order.index')}}" aria-expanded="false">
                    <i class="icon-basket menu-icon"></i><span class="nav-text">Order</span>
                </a>
            </li>
        </ul>
    </div>
</div>