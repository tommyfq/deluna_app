<script src="{{asset('plugins/common/common.min.js')}}"></script>
<script src="{{asset('js/custom.min.js')}}"></script>
<script src="{{asset('js/settings.js')}}"></script>
<script src="{{asset('js/gleek.js')}}"></script>
<script src="{{asset('js/styleSwitcher.js')}}"></script>
<script src="{{asset('plugins/highlightjs/highlight.pack.min.js')}}"></script>
<script>hljs.initHighlightingOnLoad();</script>

<script>
    (function($) {
    "use strict"

        new quixSettings({
            version: "light", //2 options "light" and "dark"
            layout: "vertical", //2 options, "vertical" and "horizontal"
            navheaderBg: "color_1", //have 10 options, "color_1" to "color_10"
            headerBg: "color_1", //have 10 options, "color_1" to "color_10"
            sidebarStyle: "full", //defines how sidebar should look like, options are: "full", "compact", "mini" and "overlay". If layout is "horizontal", sidebarStyle won't take "overlay" argument anymore, this will turn into "full" automatically!
            sidebarBg: "color_1", //have 10 options, "color_1" to "color_10"
            sidebarPosition: "fixed", //have two options, "static" and "fixed"
            headerPosition: "static", //have two options, "static" and "fixed"
            containerLayout: "wide",  //"boxed" and  "wide". If layout "vertical" and containerLayout "boxed", sidebarStyle will automatically turn into "overlay".
            direction: "ltr" //"ltr" = Left to Right; "rtl" = Right to Left
        });


    })(jQuery);
    $(document).ready(function(){
        $('.submenu.active').closest('.parentmenu').addClass('active');
        $('#menu li.active ul').addClass('collapse in');
     })
</script>
@stack('scripts')