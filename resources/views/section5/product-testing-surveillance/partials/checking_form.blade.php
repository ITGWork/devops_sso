{{-- Acceptance Form for Receiving Request --}}
{{ Form::open(['url' => 'section5/product-testing-surveillance/result-save', 'class' => 'form-horizontal', 'method' => 'POST', 'files' => true]) }}
<input type="hidden" name="id" value="{{ $item->id }}">
<input type="hidden" name="action" value="acceptance">

<div class="row">
    {{-- Include readonly sections first --}}
    @include('section5.product-testing-surveillance.partials.readonly_view')
</div>

<div class="row">
    {{-- Acceptance Section --}}
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">การรับคำขอ / รับตัวอย่าง</div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-3 control-label">สถานะปัจจุบัน :</label>
                    <div class="col-md-9">
                        <p class="form-control-static text-info">
                            <strong>{!! HP::Section5StatusProductTesting($item->status) !!}</strong>
                        </p>
                    </div>
                </div>

                @if($item->status == 1)
                <div class="form-group">
                    <div class="col-md-12 text-center">
                        <p class="text-muted m-b-20">ตรวจสอบข้อมูลและเอกสารหลักฐาน (ข้อ 7) ก่อนกระทำการรับคำขอ</p>
                        <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('ยืนยันการรับคำขอตรวจผลิตภัณฑ์?')">
                            <i class="fa fa-check-circle"></i> รับคำขอและดำเนินการต่อ
                        </button>
                        <a href="{{ url('section5/product-testing-surveillance') }}" class="btn btn-default btn-lg">ยกเลิก</a>
                    </div>
                </div>
                @else
                <div class="form-group">
                    <div class="col-md-12 text-center">
                        <a href="{{ url('section5/product-testing-surveillance/report/esurv-' . $item->id) }}" class="btn btn-info btn-lg">
                            <i class="fa fa-file-text-o"></i> ไปหน้าบันทึกผลการทดสอบ
                        </a>
                        <a href="{{ url('section5/product-testing-surveillance') }}" class="btn btn-default btn-lg">กลับ</a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{ Form::close() }}
