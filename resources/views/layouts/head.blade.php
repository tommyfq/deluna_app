<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{$_title}}</title>
<!-- Favicon icon -->
<link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon.png')}}">
<!-- Custom Stylesheet -->
<link rel="stylesheet" href="{{asset('plugins/highlightjs/styles/darkula.css')}}">
<link href="{{asset('css/style.css')}}" rel="stylesheet">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"/>
@stack('styles')