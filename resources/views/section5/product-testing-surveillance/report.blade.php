@extends('layouts.master')
@section('content')

@php
    $currentReportNo = old('report_no', $report_no ?? null);
    $overallFromDb   = $report->overall_result ?? null;
    $overallValue    = old('overall_result', $overallFromDb);

    $stdText = '-';
    if (!empty($product->tis_number)) {
        $stdText = $product->tis_number;
        if (!empty($product->tis_name)) $stdText .= ' : ' . $product->tis_name;
    }

    $licensee  = $product->coordinator ?? '-';
    $licenseNo = $product->ref_no ?? '-';
@endphp

<style>
    #tbl-test-result td, #tbl-test-result th { vertical-align: top; }
    #tbl-test-result .cell-wrap { min-width: 120px; }
    #tbl-test-result .input-sm { padding: 4px 6px; height: 28px; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">

                <h3 class="box-title pull-left">
                    รายงานผลการตรวจสอบผลิตภัณฑ์อุตสาหกรรม (เฝ้าระวัง)

                    @if(!empty($currentReportNo))
                        <span class="label label-info" style="margin-left:10px;">
                            Report No: {{ $currentReportNo }}
                        </span>
                    @endif
                </h3>

                <div class="pull-right">
                    <a href="{{ url('section5/product-testing-surveillance') }}" class="btn btn-default btn-sm">
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

                {{-- ข้อมูลคำขอ (Read-only) --}}
                <div class="panel panel-info">
                    <div class="panel-heading">ข้อมูลคำขอ (Read-only)</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">เลขที่คำขอ :</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">{{ $product->refno ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">ผู้ประกอบการ :</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">{{ $user_created->name ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">เลขผู้เสียภาษี :</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">{{ $user_created->tax_number ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">ผู้รับใบอนุญาต :</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">{{ $licensee }}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">ใบอนุญาต :</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">{{ $licenseNo }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">มาตรฐาน :</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">{{ $stdText }}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">รายละเอียดสินค้า :</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">{{ $product->product_detail ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Cross-Lab Reports --}}
                @if($other_reports->count() > 0)
                    <div class="panel panel-info">
                        <div class="panel-heading" style="background-color: #707cd2; color: #fff;">ข้อมูลจากห้องปฏิบัติการอื่นที่เกี่ยวข้อง (Cross-Lab Reports)</div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="bg-primary">
                                            <th class="text-center text-white" width="5%">#</th>
                                            <th class="text-center text-white" width="35%">ห้องปฏิบัติการ</th>
                                            <th class="text-center text-white" width="20%">เลขที่รายงาน</th>
                                            <th class="text-center text-white" width="20%">ผลการทดสอบรวม</th>
                                            <th class="text-center text-white" width="20%">รายละเอียด</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($other_reports as $index => $other)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>{{ $other->lab_name }}</td>
                                                <td class="text-center">{{ $other->report_no }}</td>
                                                <td class="text-center">
                                                    @if($other->overall_result == 'pass')
                                                        <span class="label label-success">ผ่าน</span>
                                                    @else
                                                        <span class="label label-danger">ไม่ผ่าน</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ url('section5/product-testing-surveillance/report/' . $other->product_lab_id . '?report_no=' . $other->report_no) }}"
                                                       target="_blank" class="btn btn-default btn-xs">
                                                        <i class="fa fa-eye"></i> ดูรายงาน
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- แบบฟอร์มผลการทดสอบ --}}
                {{ Form::open(['url' => route('product-testing-surveillance.report-save'), 'class' => 'form-horizontal', 'method' => 'POST', 'files' => true]) }}
                <input type="hidden" name="id" value="{{ $item->id }}">
                <input type="hidden" name="source_type" value="{{ $source_type ?? 'elicense' }}">
                @if(!empty($currentReportNo))
                    <input type="hidden" name="report_no" value="{{ $currentReportNo }}">
                @endif

                <div class="panel panel-primary">
                    <div class="panel-heading">แบบฟอร์มผลการทดสอบ</div>
                    <div class="panel-body">

                        @include('section5.product-testing-surveillance.partials.test_report_table', ['readonly' => false])

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group required">
                                    <label class="col-md-4 control-label">ผลการทดสอบรวม :</label>
                                    <div class="col-md-8">
                                        <select name="overall_result" class="form-control" required>
                                            <option value="">-- เลือก --</option>
                                            <option value="pass" {{ $overallValue == 'pass' ? 'selected' : '' }}>ผ่าน</option>
                                            <option value="fail" {{ $overallValue == 'fail' ? 'selected' : '' }}>ไม่ผ่าน</option>
                                        </select>
                                        <small class="text-muted">* ถ้า "ไม่ผ่านบางรายการ" ระบบจะส่งกลับเป็นงานทดสอบซ้ำ (เฉพาะรายการ fail)</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Full Report (PDF) :</label>
                                    <div class="col-md-8">
                                        @if(!empty($report->report_file_path))
                                            <div class="m-b-5">
                                                <a href="{{ HP::getFileStorage($report->report_file_path) }}" target="_blank" class="btn btn-info btn-sm">
                                                    <i class="fa fa-file-pdf-o"></i> ดูไฟล์ที่แนบ
                                                </a>
                                            </div>
                                        @endif
                                        <input type="file" name="full_report_pdf" class="form-control" accept="application/pdf">
                                        <small class="text-muted">แนบ PDF ฉบับเต็ม{{ !empty($report->report_file_path) ? ' — อัปโหลดใหม่เพื่อแทนไฟล์เดิม' : '' }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-success btn-lg"
                                    onclick="return confirm('ยืนยันส่งรายงานผลทดสอบ?')">
                                    <i class="fa fa-paper-plane"></i> ส่งรายงานผล
                                </button>
                                <a href="{{ url('section5/product-testing-surveillance') }}" class="btn btn-default btn-lg">
                                    ยกเลิก
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

                {{ Form::close() }}

            </div>
        </div>
    </div>
</div>
@endsection
