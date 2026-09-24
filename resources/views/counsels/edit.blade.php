@extends('layouts.master')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">ข้อเสนอแนะ e-Accreditation</h3>
                    <div class="clearfix"></div>
                    <hr>

                    @if ($errors->any())
                        <ul class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif

                    {!! Form::model($counsel, [
                        'method' => 'PATCH',
                        'url' => ['/counsels', $counsel->id],
                        'class' => 'form-horizontal',
                        'files' => true
                    ]) !!}

                    @include ('counsels.form')

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
@endsection
