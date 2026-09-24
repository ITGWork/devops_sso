@extends('layouts.master')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
                <h3 class="box-title">รายละเอียดคำขอทดสอบผลิตภัณฑ์</h3>
                <div class="pull-right">
                    <a href="javascript:history.back()" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> กลับ
                    </a>
                </div>
                <div class="clearfix"></div>
                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%" class="bg-light-part">เลขที่อ้างอิง</th>
                                <td>{{ $item->refno ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light-part">เลข มอก.</th>
                                <td>{{ $item->tis_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light-part">ชื่อ มอก.</th>
                                <td>{{ $item->tis_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light-part">ชื่อโรงงาน</th>
                                <td>{{ $item->factory_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light-part">ผู้ยื่นคำขอ</th>
                                <td>{{ $item->applicant_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light-part">เลขประจำตัวผู้เสียภาษี</th>
                                <td>{{ $item->tax_number ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%" class="bg-light-part">วันที่ยื่น</th>
                                <td>{{ !empty($item->date_sent) ? HP::DateThai($item->date_sent) : '-' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light-part">สถานะ</th>
                                <td>{{ $status_list[(string)$item->status] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light-part">ห้องปฏิบัติการ (LAB)</th>
                                <td>{{ $item->lab_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light-part">ผู้ตรวจสอบ</th>
                                <td>{{ $item->checker_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light-part">วันที่ตรวจสอบ</th>
                                <td>{{ !empty($item->checking_date) ? HP::DateThai($item->checking_date) : '-' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light-part">หมายเหตุ</th>
                                <td>{{ $item->checking_comment ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
