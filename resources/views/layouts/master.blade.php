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
                    <a href="index.html">
                        <b class="logo-abbr"><img src="images/logo.png" alt=""> </b>
                        <span class="logo-compact"><img src="./images/logo-compact.png" alt=""></span>
                        <span class="brand-title">
                            <img src="images/logo-text.png" alt="">
                        </span>
                    </a>
                </div>
            </div>

            @include('layouts.header')

            @include('layouts.sidebar')

            <div class="content-body">

                {!!$CONTENT!!}

            </div>

            @include('layouts.footer')

        </div>
    </body>

    {{-- js --}}
    @include('layouts.script')

</html>