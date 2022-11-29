<!DOCTYPE html>
<html lang="en">
    <head>
        @include('layouts.head')
    </head>

    <body>
        @include('_includes.loader')

        <div id="main-wrapper">

            <div class="nav-header">
                <div class="brand-logo">
                    <a href="javascript:void()">
                        <b class="logo-abbr"><img src="{{asset('images/logo.png')}}" alt=""> </b>
                        <span class="logo-compact"><img src="{{asset('images/logo-compact.png')}}" alt=""></span>
                        <span class="brand-title">
                            <h1 class="white">DELUNA</h1>
                        </span>
                    </a>
                </div>
            </div>

            @include('layouts.header')

            @include('layouts.sidebar')

            <div class="content-body">

                @include('_includes.breadcrumbs')

                {!!$CONTENT!!}

            </div>

            @include('layouts.footer')

        </div>
    </body>

    {{-- js --}}
    @include('layouts.script')

</html>