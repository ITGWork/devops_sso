@extends('layouts.master')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
                <h3 class="box-title">
                    รายละเอียดคำขอตรวจโรงงาน
                </h3>
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
                                <th width="40%" class="bg-light-part">วันที่ส่ง</th>
                                <td>{{ !empty($item->date_sent) ? HP::DateThai($item->date_sent) : '-' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light-part">สถานะ</th>
                                <td>
                                    @php
                                        $status_map = [1 => 'รอการตอบรับ', 2 => 'รับคำขอ', 3 => 'ไม่รับคำขอ', 4 => 'ยกเลิก'];
                                        $status_class = [1 => 'label-warning', 2 => 'label-success', 3 => 'label-danger', 4 => 'label-default'];
                                        $s = (int)$item->status;
                                    @endphp
                                    <span class="label {{ $status_class[$s] ?? 'label-default' }}">
                                        {{ $status_map[$s] ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light-part">ผู้ตรวจสอบ</th>
                                <td>{{ $item->inspector_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light-part">วันที่ตรวจสอบ</th>
                                <td>{{ !empty($item->checking_date) ? HP::DateThai($item->checking_date) : '-' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light-part">ผลการตรวจ</th>
                                <td>
                                    @php
                                        $result_map = [1 => ['text' => 'ผ่านการตรวจโรงงาน', 'class' => 'label-success'], 2 => ['text' => 'แก้ข้อบกพร่อง', 'class' => 'label-warning'], 3 => ['text' => 'ไม่ผ่านการตรวจโรงงาน', 'class' => 'label-danger']];
                                        $r = (int)$item->inspection_result;
                                    @endphp
                                    @if($r && isset($result_map[$r]))
                                        <span class="label {{ $result_map[$r]['class'] }}">{{ $result_map[$r]['text'] }}</span>
                                    @else
                                        <span class="label label-default">ยังไม่ตรวจ</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if(!empty($item->inspect_remark))
                <div class="row m-t-10">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <strong>หมายเหตุ:</strong> {{ $item->inspect_remark }}
                        </div>
                    </div>
                </div>
                @endif

                @if(!empty($item->defect))
                <div class="row m-t-10">
                    <div class="col-md-12">
                        <div class="alert alert-warning">
                            <strong>ข้อบกพร่องที่พบ:</strong> {{ $item->defect }}
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

@endsection
