@extends('layouts.master')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">แก้ไขหมวดอุตสากรรม/สาขา #{{ $branchGroup->id }}</h3>
                    @can('view-'.str_slug('branch-groups'))
                        <a class="btn btn-success pull-right" href="{{ url('/basic/branch-groups') }}">
                            <i class="icon-arrow-left-circle" aria-hidden="true"></i> กลับ
                        </a>
                    @endcan
                    <div class="clearfix"></div>
                    <hr>

                    @if ($errors->any())
                        <ul class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif

                    {!! Form::model($branchGroup, [
                        'method' => 'PATCH',
                        'url' => ['/basic/branch-groups', $branchGroup->id],
                        'class' => 'form-horizontal',
                        'files' => true
                    ]) !!}

                    @include ('basic.branch-groups.form')

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
@endsection
