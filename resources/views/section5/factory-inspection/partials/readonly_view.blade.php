{{-- Read-only view for Default mode --}}
<div class="row">
    {{-- Section 1: ข้อมูลผู้ยื่นคำขอ --}}
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">1. ข้อมูลผู้ยื่นคำขอ</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-md-4 control-label">ชื่อผู้ยื่นขอรับบริการ :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ $user_created->name ?? '-' }}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">สัญชาติ :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ $user_created->nationality ?? '-' }}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">เลขประจำตัวประชาชน :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ $user_created->id_card_no ?? '-' }}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">เลขผู้เสียภาษี :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ $user_created->tax_number ?? '-' }}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Email :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ $user_created->email ?? '-' }}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">โทรศัพท์ :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ $user_created->tel ?? '-' }}</p></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-md-4 control-label">ที่อยู่ :</label>
                            <div class="col-md-8">
                                <p class="form-control-static">
                                    {{ $user_created->address_no ?? '' }}
                                    {{ $user_created->moo ? 'หมู่ '.$user_created->moo : '' }}
                                    {{ $user_created->soi ? 'ซอย '.$user_created->soi : '' }}
                                    {{ $user_created->street ? 'ถนน '.$user_created->street : '' }}
                                    {{ $user_created->subdistrict ? 'ต.'.$user_created->subdistrict : '' }}
                                    {{ $user_created->district ? 'อ.'.$user_created->district : '' }}
                                    {{ $user_created->province ? 'จ.'.$user_created->province : '' }}
                                    {{ $user_created->zipcode ?? '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 2: ที่ตั้งสำนักงานใหญ่ --}}
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">2. ที่ตั้งสำนักงานใหญ่</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="col-md-2 control-label">ที่อยู่ :</label>
                            <div class="col-md-10">
                                <p class="form-control-static">
                                    {{ $user_created->head_address_no ?? '' }}
                                    {{ $user_created->head_moo ? 'หมู่ '.$user_created->head_moo : '' }}
                                    {{ $user_created->head_soi ? 'ซอย '.$user_created->head_soi : '' }}
                                    {{ $user_created->head_street ? 'ถนน '.$user_created->head_street : '' }}
                                    {{ $user_created->head_subdistrict ? 'ต.'.$user_created->head_subdistrict : '' }}
                                    {{ $user_created->head_district ? 'อ.'.$user_created->head_district : '' }}
                                    {{ $user_created->head_province ? 'จ.'.$user_created->head_province : '' }}
                                    {{ $user_created->head_zipcode ?? '' }}
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">โทรศัพท์ :</label>
                            <div class="col-md-4"><p class="form-control-static">{{ $user_created->head_tel ?? '-' }}</p></div>
                            <label class="col-md-2 control-label">วันที่จดทะเบียนนิติบุคคล :</label>
                            <div class="col-md-4"><p class="form-control-static">{{ HP::DateThai($user_created->date_niti ?? '') }}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">ทะเบียนเลขที่ :</label>
                            <div class="col-md-4"><p class="form-control-static">{{ $user_created->register_no ?? '-' }}</p></div>
                            <label class="col-md-2 control-label">ทะเบียนพาณิชย์เลขที่ :</label>
                            <div class="col-md-4"><p class="form-control-static">{{ $user_created->commercial_register_no ?? '-' }}</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 3: ผู้ประสานงาน --}}
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">3. ผู้ประสานงาน</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="col-md-4 control-label">ชื่อ :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ $item->factory->coordinator ?? '-' }}</p></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="col-md-4 control-label">ตำแหน่ง :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ $item->factory->coordinator_position ?? '-' }}</p></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="col-md-4 control-label">โทรศัพท์ :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ $item->factory->coordinator_tel ?? '-' }}</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 4: ข้อมูลขอรับบริการ --}}
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">4. ข้อมูลขอรับบริการ</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-md-4 control-label">คำขอที่ :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ $item->factory->refno ?? '-' }}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">วันที่ยื่น :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ HP::DateThai($item->factory->execute_date ?? '') }}</p></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-md-4 control-label">รับเมื่อ :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ HP::DateThai($item->factory->receive_date ?? '') }}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">เลขที่ มอก. :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ $item->factory->tis_number ?? '-' }} : {{ $item->factory->tis_name ?? '-' }}</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 5: ข้อมูลโรงงานที่ทำผลิตภัณฑ์ --}}
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">5. ข้อมูลโรงงานที่ทำผลิตภัณฑ์</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="col-md-2 control-label">ชื่อโรงงาน :</label>
                            <div class="col-md-10"><p class="form-control-static">{{ $item->factory->factory_name ?? '-' }}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">ที่อยู่ :</label>
                            <div class="col-md-10">
                                <p class="form-control-static">
                                    {{ $item->factory->factory_address_no ?? '' }}
                                    {{ $item->factory->factory_moo ? 'หมู่ '.$item->factory->factory_moo : '' }}
                                    {{ $item->factory->factory_soi ? 'ซอย '.$item->factory->factory_soi : '' }}
                                    {{ $item->factory->factory_street ? 'ถนน '.$item->factory->factory_street : '' }}
                                    {{ $item->factory->factory_subdistrict ? 'ต.'.$item->factory->factory_subdistrict : '' }}
                                    {{ $item->factory->factory_district ? 'อ.'.$item->factory->factory_district : '' }}
                                    {{ $item->factory->factory_province ? 'จ.'.$item->factory->factory_province : '' }}
                                    {{ $item->factory->factory_country ?? '' }}
                                    {{ $item->factory->factory_zipcode ?? '' }}
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">ทะเบียนโรงงานเลขที่ :</label>
                            <div class="col-md-4"><p class="form-control-static">{{ $item->factory->factory_register_no ?? '-' }}</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 6: สถานที่จัดเก็บผลิตภัณฑ์ --}}
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">6. สถานที่จัดเก็บผลิตภัณฑ์</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="col-md-2 control-label">ชื่อสถานที่ :</label>
                            <div class="col-md-10"><p class="form-control-static">{{ $item->factory->storage_name ?? '-' }}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">ที่อยู่ :</label>
                            <div class="col-md-10">
                                <p class="form-control-static">
                                    {{ $item->factory->storage_address_no ?? '' }}
                                    {{ $item->factory->storage_moo ? 'หมู่ '.$item->factory->storage_moo : '' }}
                                    {{ $item->factory->storage_soi ? 'ซอย '.$item->factory->storage_soi : '' }}
                                    {{ $item->factory->storage_street ? 'ถนน '.$item->factory->storage_street : '' }}
                                    {{ $item->factory->storage_subdistrict ? 'ต.'.$item->factory->storage_subdistrict : '' }}
                                    {{ $item->factory->storage_district ? 'อ.'.$item->factory->storage_district : '' }}
                                    {{ $item->factory->storage_province ? 'จ.'.$item->factory->storage_province : '' }}
                                    {{ $item->factory->storage_zipcode ?? '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 7: ประวัติการตรวจ --}}
    @if(isset($inspections) && $inspections->count() > 0)
    <div class="col-md-12">
        <div class="panel panel-warning">
            <div class="panel-heading">ประวัติการตรวจ</div>
            <div class="panel-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th>วันที่ตรวจ</th>
                            <th>ผลการตรวจ</th>
                            <th>ข้อบกพร่อง</th>
                            <th>หมายเหตุ</th>
                            <th>เอกสารแนบ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inspections as $index => $insp)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ HP::DateThai($insp->start_inspect_date) }} - {{ HP::DateThai($insp->end_inspect_date) }}</td>
                            <td>
                                @if($insp->result == 1) <span class="label label-success">ผ่าน</span>
                                @elseif($insp->result == 2) <span class="label label-warning">แก้ไขข้อบกพร่อง</span>
                                @elseif($insp->result == 3) <span class="label label-danger">ไม่ผ่าน</span>
                                @else - @endif
                            </td>
                            <td>{{ $insp->defect ?? '-' }}</td>
                            <td>{{ $insp->remark ?? '-' }}</td>
                            <td>
                                @php $attFiles = is_array($insp->att_file) ? $insp->att_file : json_decode($insp->att_file ?? '[]', true); @endphp
                                @foreach((array)$attFiles as $file)
                                    @php
                                        $attRealfile = is_array($file) ? ($file['realfile'] ?? '') : $file;
                                        $attFilename = is_array($file) ? ($file['filename'] ?? $attRealfile) : $file;
                                    @endphp
                                    <a href="{{ HP::getFileStorage('factory_inspection/' . $attRealfile) }}" target="_blank" class="btn btn-xs btn-info" title="{{ $attFilename }}"><i class="fa fa-file"></i></a>
                                @endforeach
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
