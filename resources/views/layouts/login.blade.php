<!DOCTYPE html>
<html lang="en">
    <head>
        @include('layouts.head')
    </head>

    <body>
        @include('_includes.loader')

        {!!$CONTENT!!}
       
    </body>

    {{-- js --}}
    @include('layouts.script')

</html>