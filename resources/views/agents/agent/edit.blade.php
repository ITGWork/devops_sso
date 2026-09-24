@extends('layouts.master')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">แก้ไข มอบสิทธิ์เข้าใช้งานระบบ #{{ $agent->id }}</h3>
                        <a class="btn btn-success pull-right" href="{{ url('/agents') }}">
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

                    {!! Form::model($agent, [
                        'method' => 'PATCH',
                        'url' => ['/agents', $agent->id],
                        'class' => 'form-horizontal',
                        'files' => true
                    ]) !!}

                    @include ('agents.agent.form', ['submitButtonText' => 'Update'])
      
                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
@endsection
