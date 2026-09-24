@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">ยื่นคำขอขึ้นทะเบียนผู้ตรวจ และผู้ประเมินของผู้ตรวจสอบการทำผลิตภัณฑ์อุตสาหกรรม</h3>
                        <a class="btn btn-success pull-right" href="{{url('/request_section5/application_inspectors')}}">
                            <i class="icon-arrow-left-circle"></i> กลับ</a>
                    <div class="clearfix"></div>
                    <hr>
                    @if ($errors->any())
                        <ul class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif

                    {!! Form::open(['url' => '/request_section5/application_inspectors', 'class' => 'form-horizontal', 'files' => true, 'id' => 'from_box']) !!}

                    @include ('section5.application_inspectors.form')

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
@endsection
