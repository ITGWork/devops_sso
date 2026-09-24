@extends('layouts.'.( !empty($layout)?$layout:'app' ))   

@push('css')
    <style>
        .text-bold-300 {
            font-weight: 300;
        }

        .text-bold-350 {
            font-weight: 350;
        }

        .text-bold-400 {
            font-weight: 400;
        }

        .text-bold-450 {
            font-weight: 450;
        }

        .text-bold-500 {
            font-weight: 500;
        }

        .text-bold-550 {
            font-weight: 550;
        }

        .text-bold-600 {
            font-weight: 600;
        }

        .text-bold-650 {
            font-weight: 650;
        }

        .text-bold-700 {
            font-weight: 700;
        }

        .div_dotted {
            border-top: none ;
            border-right: none ;
            border-bottom: 1px dotted;
            border-left: none ;
        }

        .font_cuttom_td{
            font-size: 14px !important;
        }

    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 {!!  !empty($layout) && $layout == 'welcome'?'white-box':'' !!}">

                <center>
                    <h3>รายละเอียดผู้ตรวจสอบการทำผลิตภัณฑ์อุตสาหกรรม</h3>
                </center>

                @php
                    $url_welcome = !empty($layout) && $layout == 'welcome'?'welcome':'';
                @endphp

                @if($type == 'scope')
                    <div class="col-md-12">
                        <a class="btn btn-success btn-sm  pull-right" href="{{ url($url_welcome.'/section5/ibcb_by_scope') }}">
                            <i class="icon-arrow-left-circle" aria-hidden="true"></i> กลับ
                        </a>
                    </div> 
                @else
                    <div class="col-md-12">
                        <a class="btn btn-success btn-sm  pull-right" href="{{ url($url_welcome.'/section5/ibcb_list') }}">
                            <i class="icon-arrow-left-circle" aria-hidden="true"></i> กลับ
                        </a>
                    </div>
                @endif

            

                @include('section5.ibcb.form.infomation')

                @include('section5.ibcb.form.scope')

                @include('section5.ibcb.form.inspectors')
                
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {

        });
    </script>
@endpush