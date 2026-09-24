<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">
    {{-- <link rel="icon" type="image/png" sizes="16x16" href="{{asset('plugins/images/favicon.png')}}">
    <title>Cubic Admin Template</title> --}}
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/logo01.png')}}">
    <title>
        {{-- {{env('APP_NAME')}} --}}
        บริการอิเล็กทรอนิกส์ สมอ.
    </title>
    <!-- ===== Bootstrap CSS ===== -->
    <link href="{{asset('bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet">
    <!-- ===== Plugin CSS ===== -->
    <link href="{{asset('plugins/components/chartist-js/dist/chartist.min.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/components/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.css')}}"
          rel="stylesheet">
    <link href="{{asset('plugins/components/toast-master/css/jquery.toast.css')}}" rel="stylesheet">

    <link href="{{asset('plugins/components/icheck/skins/all.css')}}" rel="stylesheet" type="text/css"/>
    <!-- ===== Select2 CSS ===== -->
    <link href="{{asset('plugins/components/bootstrap-select/bootstrap-select.min.css')}}" rel="stylesheet" />
    <link href="{{asset('plugins/components/custom-select/custom-select.css')}}" rel="stylesheet" type="text/css" />
    {{-- <link href="{{asset('plugins/components/select2/css/select2.min.css')}}" rel="stylesheet"> --}}

    <!-- ===== Animation CSS ===== -->
    <link href="{{asset('css/animate.css')}}" rel="stylesheet">
    <!-- ===== Custom CSS ===== -->
    <link href="{{asset('css/common.css')}}" rel="stylesheet">

    <link href="{{asset('plugins/components/parsleyjs/parsley.css?20200630')}}" rel="stylesheet" />

    <!--====== Dynamic theme changing =====-->
    @php
        $theme_name = 'default';
        $fix_header = false;
        $fix_sidebar = false;
        $theme_layout = 'normal';

        if(auth()->user()){

            $params = (object)json_decode(auth()->user()->params);

            if(!empty($params->theme_name)){
                if(is_file('css/colors/'.$params->theme_name.'.css')){
                $theme_name = $params->theme_name;
                }
            }

            if(!empty($params->fix_header) && $params->fix_header=="true"){
                $fix_header = true;
            }

            if(!empty($params->fix_sidebar) && $params->fix_sidebar=="true"){
                $fix_sidebar = true;
            }

            if(!empty($params->theme_layout)){
                $theme_layout = $params->theme_layout;;
            }
        }

        //ระยะเวลาดึงข้อมูลแจ้งเตือน
        $config = HP::getConfig();
        $refresh_notification = property_exists($config, 'refresh_notification') ? (int)$config->refresh_notification*1000 : 60000 ; //ถ้าไม่ได้ตั้งค่าใช้ 60 วิ

    @endphp

    @if($theme_layout == 'fix-header')
        <link href="{{asset('css/style-fix-header.css?20220807')}}" rel="stylesheet">
        <link href="{{asset('css/colors/'.$theme_name.'.css?20220807')}}" id="theme" data-url="{{ url('') }}" rel="stylesheet">

    @elseif($theme_layout == 'mini-sidebar')
        <link href="{{asset('css/style-mini-sidebar.css?20220807')}}" rel="stylesheet">
        <link href="{{asset('css/colors/'.$theme_name.'.css?20220807')}}" id="theme" data-url="{{ url('') }}" rel="stylesheet">
    @else
        <link href="{{asset('css/style-normal.css?20220807')}}" rel="stylesheet">
        <link href="{{asset('css/colors/'.$theme_name.'.css?20220807')}}" id="theme" data-url="{{ url('') }}" rel="stylesheet">
    @endif

    @stack('css')

    <link rel="stylesheet" href="{{asset('plugins/components/bootstrap-iconpicker/bootstrap-iconpicker.min.css?20220712')}}"/>

    <!-- ===== Color CSS ===== -->
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        @media (min-width: 768px) {
            .extra.collapse li a span.hide-menu {
                display: block !important;
            }

            .extra.collapse.in li a.waves-effect span.hide-menu {
                display: block !important;
            }

            .extra.collapse li.active a.active span.hide-menu {
                display: block !important;
            }

            ul.side-menu li:hover + .extra.collapse.in li.active a.active span.hide-menu {
                display: block !important;
            }
        }
        div.required label.control-label:after {
            content: " *";
            color: red;
        }

        #loader-container {
            width: 200px;
            height: 200px;
            color: white;
            margin: 0 auto;
            position: absolute;
            top: 50%;
            left: 50%;
            margin-right: -50%;
            transform: translate(-50%, -50%);
            border: 5px solid #3498db;
            border-radius: 50%;
            -webkit-animation: borderScale 1s infinite ease-in-out;
            animation: borderScale 1s infinite ease-in-out;
        }

        #loadingText {
            font-family: 'Raleway', sans-serif;
            font-weight: bold;
            font-size: 2em;
            position: absolute;
            top: 50%;
            left: 50%;
            margin-right: -50%;
            transform: translate(-50%, -50%);
            color: rgb(5, 5, 5);
        }

        @-webkit-keyframes borderScale {
            0% {
                border: 5px solid #99ffff;
            }
            50% {
                border: 25px solid #00e7f7;
            }
            100% {
                border: 5px solid #3498db;
            }
        }

        @keyframes borderScale {
            0% {
                border: 5px solid #99ffff;
            }
            50% {
                border: 25px solid #00e7f7;
            }
            100% {
                border: 5px solid #3498db;
            }
        }

        legend {
            width:inherit; /* Or auto */
            padding:0 10px; /* To give a bit of padding on the left and right */
            border-bottom:none;
        }

        fieldset {
            border: 1px groove #ddd !important;
            padding: 0 1.4em 1.4em 1.4em !important;
            margin: 0 0 1.5em 0 !important;
            -webkit-box-shadow:  0px 0px 0px 0px #000;
                    box-shadow:  0px 0px 0px 0px #000;
        }

        .modal-xl{
            width: 1140px;
            max-width: 1140px;
        }

        @media (min-width: 992px) {
            .modal-lg,
            .modal-xl {
                max-width: 800px;
            }

            .notification{
                width: auto;
                height: auto;
                position: fixed;
                z-index: 9999;
                top: 90%;
                right: -0.5%;
                cursor: pointer;
            }
        }

        @media (min-width: 1200px) {
            .modal-xl {
                max-width: 1140px;
            }

            .notification{
                width: auto;
                height: auto;
                position: fixed;
                z-index: 9999;
                top: 90%;
                right: -0.5%;
                cursor: pointer;
            }
        }

        @media only screen and (max-width:767px){
            .modal-xl{
                width: auto;
                max-width: 1140px;
            }

            .notification{
                width: auto;
                height: auto;
                position: fixed;
                z-index: 9999;
                top: 90%;
                right: 1%;
                cursor: pointer;
            }

            .dropleft .dropdown-menu {
                position: absolute;
                left: -290%;
                margin-bottom: 10px;
                width: 270px !important;
                max-width: 300px;
                max-height: 500px;
                overflow-y: auto;
            }
        }
        /*   Font style   */

        .icoleaf2 {
            display: inline-block;
            width: 50px;
            height: 50px;
            padding: 5px 12px;
            font-size: 28px;
            border-top-left-radius: 50%;
            border-bottom-left-radius: 50%;
            border-bottom-right-radius: 50%;
        }

        /* _dropdown.scss:73 */
        .dropleft .dropdown-menu {
            position: absolute;
            left: -325%;
            margin-bottom: 10px;
            width: 300px !important;
            max-height: 727px;
            max-width: 300px;
            overflow-y: auto;
        }

        .dropleft .dropdown-menu > li > a{
            white-space:normal;
        }
    </style>
</head>

<body class="
  {{ $theme_layout }}
  @if($fix_header===true) fix-header @endif
  @if($fix_sidebar===true) fix-sidebar @endif">
<!-- ===== Main-Wrapper ===== -->
<div id="wrapper">
    <div class="preloader">
        <div class="cssload-speeding-wheel"></div>
    </div>
    <!-- ===== Top-Navigation ===== -->
    @include('layouts.partials.navbar')
    <!-- ===== Top-Navigation-End ===== -->

        <!-- ===== Left-Sidebar ===== -->
    @include('layouts.partials.sidebar')
    @include('layouts.partials.right-sidebar')

    @include('layouts.notice.notification')

<!-- ===== Left-Sidebar-End ===== -->
    <!-- ===== Page-Content ===== -->
    <div class="page-wrapper">

        @yield('content')

        <footer class="footer t-a-c">
            © 2565 สมอ.
        </footer>
    </div>
    <!-- ===== Page-Content-End ===== -->
</div>
<!-- ===== Main-Wrapper-End ===== -->
<!-- ==============================
    Required JS Files
=============================== -->
<!-- ===== jQuery ===== -->
<script src="{{asset('plugins/components/jquery/dist/jquery.min.js')}}"></script>
<!-- ===== Bootstrap JavaScript ===== -->
<script src="{{asset('bootstrap/dist/js/bootstrap.min.js')}}"></script>
<!-- ===== Slimscroll JavaScript ===== -->
<script src="{{asset('js/jquery.slimscroll.js')}}"></script>
<!-- ===== Wave Effects JavaScript ===== -->
<script src="{{asset('js/waves.js')}}"></script>
<!-- ===== Menu Plugin JavaScript ===== -->
<script src="{{asset('js/sidebarmenu.js')}}"></script>
<!-- ===== Custom JavaScript ===== -->
@if($theme_layout == 'fix-header')
    <script src="{{asset('js/custom-fix-header.js')}}"></script>
@elseif($theme_layout == 'mini-sidebar')
    <script src="{{asset('js/custom-mini-sidebar.js')}}"></script>
@else
    <script src="{{asset('js/custom-normal.js')}}"></script>
@endif

{{--<script src="{{asset('js/custom.js')}}"></script>--}}
<!-- ===== Plugin JS ===== -->
<script src="{{asset('plugins/components/chartist-js/dist/chartist.min.js')}}"></script>
<script src="{{asset('plugins/components/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.min.js')}}"></script>
<script src="{{asset('plugins/components/sparkline/jquery.sparkline.min.js')}}"></script>
<script src="{{asset('plugins/components/sparkline/jquery.charts-sparkline.js')}}"></script>
<script src="{{asset('plugins/components/knob/jquery.knob.js')}}"></script>
<script src="{{asset('plugins/components/easypiechart/dist/jquery.easypiechart.min.js')}}"></script>
<!-- ===== Style Switcher JS ===== -->
<script src="{{asset('plugins/components/styleswitcher/jQuery.style.switcher.js')}}"></script>
<script src="{{asset('plugins/components/bootstrap-iconpicker/bootstrap-iconpicker-iconset-all.min.js')}}"></script>
<script src="{{asset('plugins/components/bootstrap-iconpicker/bootstrap-iconpicker.min.js')}}"></script>

<!-- ===== select 2  ===== -->
<script src="{{ asset('plugins/components/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>

{{-- <script src="{{asset('plugins/components/select2/js/select2.min.js')}}"></script> --}}

<script src="{{asset('plugins/components/parsleyjs/parsley.min.js')}}"></script>
<script src="{{asset('plugins/components/parsleyjs/language/th.js')}}"></script>

<script src="{{ asset('plugins/components/icheck/icheck.min.js') }}"></script>
<script src="{{ asset('plugins/components/icheck/icheck.init.js') }}"></script>

<script src="{{asset('js/jasny-bootstrap.js')}}"></script>

<script src="{{asset('plugins/components/sweet-alert2/sweetalert2.all.min.js')}}"></script>


<script type="text/javascript">
    $(document).ready(function() {
        // Stuff to do as soon as the DOM is ready
        $("select:not(.not_select2)").select2({
            dropdownAutoWidth: true,
            width: '100%'
        });
            //Validate
        if($('form').length > 0){
            $('form:first:not(.not_validated)').parsley().on('field:validated', function() {
                var ok = $('.parsley-error').length === 0;
                $('.bs-callout-info').toggleClass('hidden', !ok);
                $('.bs-callout-warning').toggleClass('hidden', ok);
            })
            .on('form:submit', function() {
            return true; // Don't submit form for this demo
            });
        }


        LoadNotificaion( true );

        setInterval(function(){
            LoadNotificaion( false );
        }, {{ $refresh_notification }});


        $('#dropNotificaton').click(function (e) {
            var id = [];
            $('.input_read_all').each(function(index, element){
                id.push($(element).val());
            });

            if(id.length > 0){
                $.ajax({
                    type:"POST",
                    url:  "{{ url('/funtions/read_all/notification') }}",
                    data:{
                        _token: "{{ csrf_token() }}",
                        id: id
                    },
                    success:function(data){
                        LoadNotificaion( false );
                    }
                });
            }

        });

    });

    function LoadNotificaion( check  ){

        if( check == true ){
            $('.notification').hide();
        }

        $.ajax({
            url: "{!! url('/funtions/auto-refresh/notification') !!}"
        }).done(function( object ) {
            $('.notifiction_badge').hide();
            $('.notifiction_details').html('');

            if( object.length > 0 ){
                var html = '';
                var i = 0;
                $.each(object, function( index, data ) {

                    var input = '';

                    if(  data.read_all != 1  ){
                        i++;
                        input = '<input type="hidden" class="input_read_all" value="'+(data.id)+'">';
                    }

                    var status = '';

                    if( data.type == 2 ){
                        status += '<span class="h6">คุณได้รับการมอบหมาย </span><br>';
                    }else if( data.type == 3 ){
                        status += '<span class="h6">คุณได้อนุมัติ</span><br>';
                    }else if( data.type == 4  ){
                        status += '<span class="h6">คุณได้บันทึกข้อมูล</span><br>';
                    }else{
                        status += '<span class="h6">สถานะ : '+(data.ref_status)+' </span><br>';
                    }

                    var style = ( data.read != 1 )?'<span class="fa fa-circle text-success m-r-10 pull-right"></span>':'';

                    var details = '<div class="mail-contnet" >';
                        details += '<h5 class="">'+(data.ref_applition_no)+' '+style+'</h5>';
                        details += status;
                        details += '<span class="h6">'+(data.title)+' </span><br>';
                        details += '<span class="time">'+(data.created_ats)+'</span>';
                        details += '</div>';
                    html += '<li ><a href="'+(data.root_site)+'/funtions/redirect/notification/'+(data.id)+'" target="_blank">'+(details)+'</a>'+(input)+'</li>';
                });

                html += '<li role="separator" class="divider"></li>';
                html += '<li class="text-center"><a href="#">ดูทั้งหมด</a></li>';

                if( i == 0){
                    $('.dropleft > .dropdown-menu').css("left", "-500%");
                    $('.notification').css("right", "1%");
                }else{
                    $('.dropleft > .dropdown-menu').css("left", "-325%");
                    $('.notifiction_badge').text(i);
                    $('.notifiction_badge').show();
                }

                $('.notifiction_details').html(html);

                $('.notification').show();

            }else{
                $('.notification').hide();
            }

        });

    }
</script>


@stack('js')
</body>
<script type="text/javascript">

    $(document).ready(function() {

        // Stuff to do as soon as the DOM is ready
        $("select:not(.not_select2)").select2({
            dropdownAutoWidth: true,
            width: '100%'
        });


    });
</script>


</html>
