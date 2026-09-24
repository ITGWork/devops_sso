@extends('layouts.master')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
                <h3 class="box-title">รับคำขอตรวจโรงงาน</h3>
                <div class="clearfix"></div>
                <hr>
                <div class="row colorbox-group-widget">

                    <div class="col-md-4 col-sm-6 info-color-box waves-effect waves-light">
                        <a href="{{ url('section5/factory-inspection?type=elicense') }}">
                            <div class="white-box">
                                <div class="media bg-primary">
                                    <div class="media-body">
                                        <h3 class="info-count text-white">รับคำขอตรวจโรงงาน<br/>E-license<br/>
                                            <span class="pull-right" style="font-size:45px;"><i class="mdi mdi-factory"></i></span>
                                        </h3>
                                        <p class="info-text font-12 text-white">ระบบรับคำขอตรวจโรงงาน E-license</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-4 col-sm-6 info-color-box waves-effect waves-light">
                        <a href="{{ url('section5/factory-inspection?type=esurveillance') }}">
                            <div class="white-box">
                                <div class="media bg-success">
                                    <div class="media-body">
                                        <h3 class="info-count text-white">รับคำขอตรวจโรงงาน<br/>E-surveillance<br/>
                                            <span class="pull-right" style="font-size:45px;"><i class="mdi mdi-factory"></i></span>
                                        </h3>
                                        <p class="info-text font-12 text-white">ระบบรับคำขอตรวจโรงงาน E-surveillance</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
