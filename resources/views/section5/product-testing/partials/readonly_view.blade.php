{{-- Read-only view: ข้อมูลคำขอทดสอบผลิตภัณฑ์ --}}
<div class="row">
    {{-- 1: ข้อมูลผู้ยื่นคำขอ --}}
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

    {{-- 2: ที่ตั้งสำนักงานใหญ่ --}}
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
                    <label class="col-md-2 control-label">วันที่จดทะเบียน :</label>
                    <div class="col-md-4"><p class="form-control-static">{{ HP::DateThai($user_created->date_niti ?? '') }}</p></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3: ผู้ประสานงาน --}}
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">3. ผู้ประสานงาน</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="col-md-4 control-label">ชื่อ :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ $item->product->coordinator ?? '-' }}</p></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="col-md-4 control-label">ตำแหน่ง :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ $item->product->coordinator_position ?? '-' }}</p></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="col-md-4 control-label">โทรศัพท์ :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ $item->product->coordinator_tel ?? '-' }}</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4: ข้อมูลขอรับบริการ --}}
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">4. ข้อมูลขอรับบริการ</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-md-4 control-label">คำขอที่ :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ $item->product->refno ?? '-' }}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">วันที่ยื่น :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ HP::DateThai($item->product->execute_date ?? '') }}</p></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-md-4 control-label">เลขที่ มอก. :</label>
                            <div class="col-md-8"><p class="form-control-static">{{ $item->product->tis_number ?? '-' }} : {{ $item->product->tis_name ?? '-' }}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">สถานะ :</label>
                            <div class="col-md-8">
                                @php
                                    $sl = ['1'=>'รอการตอบรับ','2'=>'รับคำขอ','3'=>'ไม่รับคำขอ','4'=>'ยกเลิก','5'=>'อยู่ระหว่างการทดสอบ','6'=>'ขอตัวอย่างเพิ่มเติม','7'=>'แจ้งผล','8'=>'สรุปผล'];
                                @endphp
                                <p class="form-control-static"><strong>{{ $sl[(string)$item->status] ?? '-' }}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 5: ข้อมูลโรงงาน --}}
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">5. ข้อมูลโรงงานที่ทำผลิตภัณฑ์</div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-2 control-label">ชื่อโรงงาน :</label>
                    <div class="col-md-10"><p class="form-control-static">{{ $item->product->factory_name ?? '-' }}</p></div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ที่อยู่ :</label>
                    <div class="col-md-10">
                        <p class="form-control-static">
                            {{ $item->product->factory_address_no ?? '' }}
                            {{ $item->product->factory_moo ? 'หมู่ '.$item->product->factory_moo : '' }}
                            {{ $item->product->factory_soi ? 'ซอย '.$item->product->factory_soi : '' }}
                            {{ $item->product->factory_street ? 'ถนน '.$item->product->factory_street : '' }}
                            {{ $item->product->factory_subdistrict ? 'ต.'.$item->product->factory_subdistrict : '' }}
                            {{ $item->product->factory_district ? 'อ.'.$item->product->factory_district : '' }}
                            {{ $item->product->factory_province ? 'จ.'.$item->product->factory_province : '' }}
                            {{ $item->product->factory_zipcode ?? '' }}
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ทะเบียนโรงงานเลขที่ :</label>
                    <div class="col-md-4"><p class="form-control-static">{{ $item->product->factory_register_no ?? '-' }}</p></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 6: สถานที่จัดเก็บผลิตภัณฑ์ --}}
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">6. สถานที่จัดเก็บผลิตภัณฑ์</div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-2 control-label">ชื่อสถานที่ :</label>
                    <div class="col-md-10"><p class="form-control-static">{{ $item->product->storage_name ?? '-' }}</p></div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ที่อยู่ :</label>
                    <div class="col-md-10">
                        <p class="form-control-static">
                            {{ $item->product->storage_address_no ?? '' }}
                            {{ $item->product->storage_moo ? 'หมู่ '.$item->product->storage_moo : '' }}
                            {{ $item->product->storage_soi ? 'ซอย '.$item->product->storage_soi : '' }}
                            {{ $item->product->storage_street ? 'ถนน '.$item->product->storage_street : '' }}
                            {{ $item->product->storage_subdistrict ? 'ต.'.$item->product->storage_subdistrict : '' }}
                            {{ $item->product->storage_district ? 'อ.'.$item->product->storage_district : '' }}
                            {{ $item->product->storage_province ? 'จ.'.$item->product->storage_province : '' }}
                            {{ $item->product->storage_zipcode ?? '' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 7: หลักฐานเอกสาร --}}
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">7. หลักฐานเอกสาร</div>
            <div class="panel-body">
                @php
                    $evidences = [
                        'evidence_first_one'   => 'เอกสารแสดงตน 1',
                        'evidence_first_two'   => 'เอกสารแสดงตน 2',
                        'evidence_first_three' => 'เอกสารแสดงตน 3',
                        'evidence_first_four'  => 'เอกสารแสดงตน 4',
                        'evidence_second'      => 'หลักฐานที่ 2',
                        'evidence_third'       => 'หลักฐานที่ 3',
                        'evidence_fourth'      => 'หลักฐานอื่นๆ',
                    ];
                @endphp
                @foreach($evidences as $field => $label)
                    @php
                        $rawValue = $item->product->$field ?? null;
                        if (is_array($rawValue)) {
                            $files = $rawValue;
                        } elseif (is_string($rawValue) && !empty($rawValue)) {
                            $decoded = json_decode($rawValue, true);
                            $files   = is_array($decoded) ? $decoded : [$rawValue];
                        } else {
                            $files = [];
                        }
                    @endphp
                    @if(!empty($files))
                        <div class="form-group">
                            <label class="col-md-2 control-label">{{ $label }} :</label>
                            <div class="col-md-10">
                                @foreach((array)$files as $file)
                                    @if(is_string($file))
                                        <a href="{{ asset('uploads/product/' . $file) }}" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-file"></i> {{ basename($file) }}</a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
