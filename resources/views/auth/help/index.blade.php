@extends('layouts.welcome')

@section('content')

    <style>

        .panel-default{
            border: 1px solid #c4baba;
        }
        .panel-body{
            padding: 5px 25px 15px 25px !important;
        }

    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h2>
                        <i class="mdi mdi-help-circle"></i>พบปัญหาการใช้งาน
                    </h2>
                    <hr class="m-t-0" />

                    @if(  Schema::hasTable((new App\Models\Config\ConfigsFaqs)->getTable()) )
                        
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                            @foreach ( App\Models\Config\ConfigsFaqs::where('state', 1)->get() as $key => $faqs )
                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab" id="heading_{!! $faqs->id !!}">
                                        <h4 class="panel-title">
                                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_{!! $faqs->id !!}" aria-expanded="false" aria-controls="collapse_{!! $faqs->id !!}">
                                               #{!! ++$key !!} {!! !empty( $faqs->title )?$faqs->title:'-' !!}
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapse_{!! $faqs->id !!}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading_{!! $faqs->id !!}">
                                        <div class="panel-body">
                                            {!! !empty( $faqs->description )?$faqs->description:'-' !!}

                                            @if( isset($faqs->attach_file_faqs) && count($faqs->attach_file_faqs) >= 1 )
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <ul class="list-unstyled">
                                                            <li><h6> ดาวน์โหลไฟล์ </h6></li>
                                                            <li>
                                                                <ul class="list-styled">
                                                                    @foreach ( $faqs->attach_file_faqs as $File )
                                                                        <li class="form-group">
                                                                
                                                                            <a href=" {!! HP::getFileStorage($File->url) !!}" target="_blank" title="{!! !empty($File->filename) ? $File->filename : 'ไฟล์แนบ' !!}">
                                                                                {!! !empty($File->filename) ? $File->filename : 'ไฟล์แนบ' !!}
                                                                            </a>                                                            
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
            
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingOne">
                                    <h4 class="panel-title">
                                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                            #1 ลงทะเบียนแล้วแต่ไม่ได้รับอีเมลเพื่อยืนยันตัวตน
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                    <div class="panel-body">
                                        เมื่อท่านลงทะเบียนแล้วแต่ไม่ได้รับอีเมลเพื่อยืนยันตัวตน สามารถร้องขออีเมลจากระบบโดยกรอกแบบฟอร์มได้จากลิงค์นี้
                                        <a href="{{ url('reset-email') }}" target="_blank"><i class="mdi mdi-hand-pointing-right"></i> ลิงค์</a>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingTwo">
                                    <h4 class="panel-title">
                                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                            #2 ลืมชื่อผู้ใช้งาน
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                    <div class="panel-body">
                                        <ul class="p-l-20">
                                            <li>
                                                ชื่อผู้ใช้งาน ระบบจะกำหนดให้ใช้เลขประจำตัวประชาชน, เลขประจำตัวผู้เสียภาษี หรือเลขที่หนังสือเดินทางในกรณีชาวต่างชาติ
                                            </li>
                                            <li>ในกรณีลงทะเบียนเป็นสาขา (เจ้าหน้าที่สมอ.เป็นผู้ดำเนินการให้) จะใช้เลขประจำตัวผู้เสียภาษี + รหัสสาขา เช่น เลขประจำตัวผู้เสียภาษี 9999999999999 รหัสสาขา 0002 จะได้ชื่อผู้ใช้งาน เป็น <span class="text-dark">9999999999999</span><span class="text-info">0002</span></li>
                                            <li>หากท่านลืมชื่อผู้ใช้งาน สามารถร้องขอข้อมูล ระบบจะส่งข้อมูลให้ท่านทางอีเมล โดยกรอกอีเมลในแบบฟอร์มจากลิงค์นี้
                                                <a href="{{ url('forgot-user') }}" target="_blank"><i class="mdi mdi-hand-pointing-right"></i> ลิงค์</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingThree">
                                    <h4 class="panel-title">
                                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                            #3 ลืมอีเมลที่ใช้ลงทะเบียน
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                                    <div class="panel-body">
                                        คุณสามารถตรวจสอบอีเมลที่ใช้ลงทะเบียนโดยกรอกเลขประจำตัวประชาชน, เลขประจำตัวผู้เสียภาษี หรือเลขที่หนังสือเดินทางในแบบฟอร์มจากลิงค์นี้
                                        <a href="{{ url('check-email') }}" target="_blank"><i class="mdi mdi-hand-pointing-right"></i> ลิงค์</a>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="heading4">
                                    <h4 class="panel-title">
                                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                            #4 ลืมรหัสผ่าน
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapse4" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading4">
                                    <div class="panel-body">
                                        คุณสามารถร้องขอลิงค์เพื่อรีเซตรหัสผ่าน ระบบจะส่งลิงค์สำหรับรีเซตรหัสผ่านให้ท่านทางอีเมล โดยกรอกอีเมลในแบบฟอร์มจากลิงค์นี้
                                        <a href="{{ url('password/reset') }}" target="_blank"><i class="mdi mdi-hand-pointing-right"></i> ลิงค์</a>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="heading5">
                                    <h4 class="panel-title">
                                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse5" aria-expanded="false" aria-controls="collapse5" style="text-transform:none;">
                                            #5 ต้องการเปลี่ยน e-Mail ผู้ติดต่อ
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapse5" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading5">
                                    <div class="panel-body">
                                        เมื่อมีการเปลี่ยนข้อมูลผู้ติดต่อ และต้องการเปลี่ยนแปลง e-Mail ที่รับข้อมูลในระบบ มีรายละเอียดการดำเนินการ ตามลิงค์นี้
                                        <a href="{{ asset('downloads/manual/การขอเปลี่ยน_e-Mail_ในระบบ_SSO_R1.pdf') }}" target="_blank"><i class="mdi mdi-hand-pointing-right"></i> ลิงค์</a>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="heading6">
                                    <h4 class="panel-title">
                                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse6" aria-expanded="false" aria-controls="collapse6" style="text-transform:none;">
                                            #6 เปิดใช้งานการยืนยัน 2 ขั้นตอน (2FA Google Authenticator) แล้วโทรศัพท์ใช้งานไม่ได้หรือสูญหาย
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapse6" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading6">
                                    <div class="panel-body">
                                        หากคุณเปิดใช้งาน 2FA Google Authenticator แล้วโทรศัพท์ใช้งานไม่ได้หรือสูญหาย ให้ติดต่อสำนักงานมาตรฐานผลิตภัณฑ์อุตสาหกรรม (สมอ.) เพื่อ<b>ปิด</b>การใช้งานการยืนยัน 2 ขั้นตอนไปก่อน คุณสามารถเปิดใช้ได้อีกครั้งหลังเข้าสู่ระบบ ข้อมูลการติดต่อคลิกลิงค์นี้
                                        <a href="{{ url('contact') }}" target="_blank"><i class="mdi mdi-hand-pointing-right"></i> ลิงค์</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

            </div>
        </div>
    </div>

@endsection

@push('js')

@endpush
