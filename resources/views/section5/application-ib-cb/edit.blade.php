@extends('layouts.master')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">แก้ไขคำขอรับการแต่งตั้งเป็นผู้ตรวจสอบการทำผลิตภัณฑ์อุตสาหกรรม (IB/CB) #{{ $applicationibcb->id }}</h3>
                        <a class="btn btn-success pull-right" href="{{ url('/request-section-5/application-ibcb') }}">
                            <i class="icon-arrow-left-circle" aria-hidden="true"></i> กลับ</a>

                    <div class="clearfix"></div>
                    <hr>

                    @if ($errors->any())
                        <ul class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif

                    {!! Form::model($applicationibcb, [
                        'method' => 'PATCH',
                        'url' => ['/request-section-5/application-ibcb', $applicationibcb->id],
                        'class' => 'form-horizontal',
                        'files' => true,
                        'id' => 'from_box'
                    ]) !!}

                    @include ('section5.application-ib-cb.form')

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
@endsection
