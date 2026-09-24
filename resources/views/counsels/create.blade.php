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
                    
                    @if(\Session::has('flash_message'))
                        @include ('counsels.save-success')
                    @else
                        {!! Form::open(['url' => '/counsels', 'class' => 'form-horizontal', 'files' => true, 'id' => 'from_box']) !!}

                            @include ('counsels.form')

                        {!! Form::close() !!}
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
