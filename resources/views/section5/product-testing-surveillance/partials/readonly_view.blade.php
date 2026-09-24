{{-- Read-only view: ข้อมูลคำขอเฝ้าระวังทดสอบผลิตภัณฑ์ --}}
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

    {{-- Section 3: ผู้ประสานงาน --}}
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">3. ผู้ประสานงาน</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="col-md-4 control-label">ชื่อ :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ $product->coordinator ?? '-' }}</p></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="col-md-4 control-label">ตำแหน่ง :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ $product->coordinator_position ?? '-' }}</p></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="col-md-4 control-label">โทรศัพท์ :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ $product->coordinator_tel ?? '-' }}</p></div>
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
                            <div class="col-md-8"><p class="form-control-static">{{ $product->refno ?? '-' }}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">วันที่ยื่น :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ HP::DateThai($product->execute_date ?? '') }}</p></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-md-4 control-label">รับเมื่อ :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ HP::DateThai($product->receive_date ?? '') }}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">เลขที่ มอก. :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ $product->tis_number ?? '-' }} : {{ $product->tis_name ?? '-' }}</p></div>
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
                <div class="form-group">
                    <label class="col-md-2 control-label">ชื่อโรงงาน :</label>
                    <div class="col-md-10"><p class="form-control-static">{{ $product->factory_name ?? '-' }}</p></div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ที่อยู่ :</label>
                    <div class="col-md-10">
                        <p class="form-control-static">
                            {{ $product->factory_address_no ?? '' }}
                            {{ $product->factory_moo ? 'หมู่ '.$product->factory_moo : '' }}
                            {{ $product->factory_soi ? 'ซอย '.$product->factory_soi : '' }}
                            {{ $product->factory_street ? 'ถนน '.$product->factory_street : '' }}
                            {{ $product->factory_subdistrict ? 'ต.'.$product->factory_subdistrict : '' }}
                            {{ $product->factory_district ? 'อ.'.$product->factory_district : '' }}
                            {{ $product->factory_province ? 'จ.'.$product->factory_province : '' }}
                            {{ $product->factory_zipcode ?? '' }}
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ทะเบียนโรงงานเลขที่ :</label>
                    <div class="col-md-4"><p class="form-control-static">{{ $product->factory_register_no ?? '-' }}</p></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 6: สถานที่จัดเก็บผลิตภัณฑ์ --}}
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">6. สถานที่จัดเก็บผลิตภัณฑ์</div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-2 control-label">ชื่อสถานที่ :</label>
                    <div class="col-md-10"><p class="form-control-static">{{ $product->storage_name ?? '-' }}</p></div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ที่อยู่ :</label>
                    <div class="col-md-10">
                        <p class="form-control-static">
                            {{ $product->storage_address_no ?? '' }}
                            {{ $product->storage_moo ? 'หมู่ '.$product->storage_moo : '' }}
                            {{ $product->storage_soi ? 'ซอย '.$product->storage_soi : '' }}
                            {{ $product->storage_street ? 'ถนน '.$product->storage_street : '' }}
                            {{ $product->storage_subdistrict ? 'ต.'.$product->storage_subdistrict : '' }}
                            {{ $product->storage_district ? 'อ.'.$product->storage_district : '' }}
                            {{ $product->storage_province ? 'จ.'.$product->storage_province : '' }}
                            {{ $product->storage_zipcode ?? '' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 7: ประวัติการตรวจ (if any) --}}
    @if(isset($inspections) && $inspections->count() > 0)
    <div class="col-md-12">
        <div class="panel panel-warning">
            <div class="panel-heading">7. ประวัติการตรวจ</div>
            <div class="panel-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th>วันที่ตรวจ</th>
                            <th>ผลการตรวจ</th>
                            <th>ข้อบกพร่อง</th>
                            <th>หมายเหตุ</th>
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
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
