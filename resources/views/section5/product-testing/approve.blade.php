@extends('layouts.master')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">
                        บันทึกผลการทดสอบผลิตภัณฑ์
                    </h3>
                    <div class="pull-right">
                        @if($item->status == 2)
                            <a href="{{ url('section5/product-testing/report/' . $item->id) }}" class="btn btn-success btn-sm">
                                <i class="fa fa-file-text-o"></i> บันทึกผล/รายงานผล
                            </a>
                        @endif
                        <a href="{{ url('section5/product-testing?type=elicense') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-arrow-left"></i> กลับ
                        </a>
                    </div>
                    <div class="clearfix"></div>
                    <hr>

                    @if(session('flash_message'))
                        <div class="alert alert-success">{{ session('flash_message') }}</div>
                    @endif
                    @if(session('error_message'))
                        <div class="alert alert-danger">{{ session('error_message') }}</div>
                    @endif

                    {{-- Readonly product sections --}}
                    @include('section5.product-testing.partials.readonly_view')

                    {{-- Show test report read-only if status >= 8 --}}
                    @if(!empty($report))
                        <div class="panel panel-info">
                            <div class="panel-heading">ผลการทดสอบผลิตภัณฑ์</div>
                            <div class="panel-body">
                                @include('section5.product-testing.partials.test_report_table', ['readonly' => true])
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
