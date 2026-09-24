@push('css')
<link href="{{asset('plugins/components/icheck/skins/all.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{asset('plugins/components/bootstrap-datepicker-thai/css/datepicker.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('plugins/components/switchery/dist/switchery.min.css')}}" rel="stylesheet" />

@endpush

<div class="row" style="margin-top:-100px;">
    <div class="col-md-12">

        @php
            $file_agens = [];
            if( isset($agent) && !empty($agent) ){
                $file_agens = App\AttachFile::select('url','caption','filename','file_properties')->where('section', 'file_attach')->where('ref_table', (new App\Models\Agents\Agent )->getTable() )->where('ref_id', $agent->id )->get();
            }

        @endphp

        @if (count($file_agens) > 0)
            @foreach($file_agens as $file_agen)
                    <div class="form-group">
                        {!! HTML::decode(Form::label('personfile', 'เอกสารแนบที่เกี่ยวข้อง'.' : ', ['class' => 'col-md-2 m-t-9 control-label'])) !!}
                            <div class="col-md-10 m-t-10">
                            @if( HP::checkFileStorage($file_agen->url) )
                                <a href="{{url('funtions/get-view/'.$file_agen->url.'/'.( !empty($file_agen->filename) ? $file_agen->filename :  basename($file_agen->url)  ))}}" target="_blank">
                                    {{  !empty($file_agen->caption) ? $file_agen->caption.('.'.$file_agen->file_properties ?? '' ):  (  !empty($file_agen->filename) ? $file_agen->filename : 'ไฟล์แนบ' )  }}
                                </a>
                            @else
                                -
                            @endif
                        </div>
                    </div>
            @endforeach
        @endif


        <div class="form-group">
            {!! HTML::decode(Form::label('personfile', 'วันที่มอบสิทธิ์'.' : '.'<span class="text-danger">*</span>', ['class' => 'col-md-2 m-t-9 control-label'])) !!}
                 <div class="col-md-4">
                    {!! Form::text('created_at',  !empty($agent->created_at) ? HP::DateTimeThaiTormat_1($agent->created_at) : null, ['class' => 'form-control','placeholder' =>'วันที่มอบสิทธิ์','disabled'=>true]) !!}
                    {!! $errors->first('created_at', '<p class="help-block">:message</p>') !!}
            </div>
        </div>

    </div>
</div>

<div class="white-box">
    <div class="row">
        <div class="col-sm-12">
            <legend><h4>ยืนยันการรับมอบสิทธิ์</h4></legend>

            <div class="row">
                <div class="col-md-12">
                    <div class="    {{ $errors->has('confirm_status') ? 'has-error' : ''}}">
                        {!! HTML::decode(Form::label('confirm_status', 'สถานะการยืนยัน'.' : '.'<span class="text-danger">*</span>', ['class' => 'col-md-4 control-label'])) !!}
                        <div class="form-group col-md-4">
                            {!! Form::radio('confirm_status', '1', true, ['class'=>'check confirm_status', 'data-radio'=>'iradio_square-green','id'=>'confirm_status1']) !!}
                            <label for="confirm_status1">&nbsp;ยืนยัน&nbsp;&nbsp;</label>
                            {!! Form::radio('confirm_status', '2', false, ['class'=>'check confirm_status', 'data-radio'=>'iradio_square-green','id'=>'confirm_status2']) !!}
                            <label for="confirm_status2">&nbsp;ไม่ยืนยัน&nbsp;&nbsp;</label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <div class="row">
                <div class="col-md-12">
                    <div class="    {{ $errors->has('attach') ? 'has-error' : ''}}">
                        {!! HTML::decode(Form::label('attach', 'เอกสารแนบ'.' : ', ['class' => 'col-md-4 control-label'])) !!}
                        <div class="form-group col-md-8">

                            @if (!Is_null($agent->FileAttachSection2To) && HP::checkFileStorage($agent->FileAttachSection2To->url))
                                @php
                                   $attach =  $agent->FileAttachSection2To;
                                @endphp
                                <a  class="m-t-10" href="{{url('funtions/get-view/'.$attach->url.'/'.( !empty($attach->filename) ? $attach->filename :  basename($attach->url)  ))}}" target="_blank">
                                    {{  !empty($attach->caption) ? $attach->caption.('.'.$attach->file_properties ?? '' ):  (  !empty($attach->filename) ? $attach->filename : 'ไฟล์แนบ' )  }}
                                </a>
                            @else
                            <div class="col-md-4">
                                {!! Form::text('file_attach',null , ['class' => 'form-control']) !!}
                            </div>
                            <div class="col-md-4">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                    <div class="form-control" data-trigger="fileinput">
                                        <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                        <span class="fileinput-filename"></span>
                                    </div>
                                    <span class="input-group-addon btn btn-default btn-file">
                                        <span class="fileinput-new">เลือกไฟล์</span>
                                        <span class="fileinput-exists">เปลี่ยน</span>
                                        <input type="file" name="attach" id="attach"   >
                                    </span>
                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                                </div>
                            </div>

                            @endif

                        </div>
                    </div>
                </div>
            </div> --}}

            <div class="row">
                <div class="col-md-12">
                    <div class="    {{ $errors->has('confirm_date') ? 'has-error' : ''}}">
                        {!! HTML::decode(Form::label('confirm_date', 'วันที่ยืนยัน'.' : ', ['class' => 'col-md-4 control-label'])) !!}
                        <div class="form-group col-md-4">
                            {!! Form::text('confirm_date',  !empty($agent->confirm_date) ? HP::DateTimeThaiTormat_1($agent->confirm_date) : HP::DateTimeThaiTormat_1(date('Y-m-d H:i:s')), ['class' => 'form-control','placeholder' =>'วันที่มอบสิทธิ์','disabled'=>true]) !!}
                            {!! $errors->first('confirm_date', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="previousUrl" id="previousUrl" value="{{  app('url')->previous()  }}">
        @if (!empty($agent->confirm_status))
                <div class="form-group">
                    <div class="col-md-offset-4 col-md-4">
                        <a  class="btn btn-default btn-lg btn-block" href="{{ app('url')->previous()  }}">
                            <i class="fa fa-rotate-left"></i> ยกเลิก
                        </a>
                    </div>
                </div>
        @else
                <div class="form-group">
                    <div class="col-md-offset-4 col-md-4">
                        <button class="btn btn-primary" type="button" id="btn_submit">
                        <i class="fa fa-paper-plane"></i> บันทึก
                        </button>
                        <a class="btn btn-default" href="{{ app('url')->previous()  }}">
                            <i class="fa fa-rotate-left"></i> ยกเลิก
                        </a>
                    </div>
                </div>
        @endif


        </div>
    </div>
</div>

@push('js')
    <script src="{{ asset('plugins/components/icheck/icheck.min.js') }}"></script>
    <script src="{{ asset('plugins/components/icheck/icheck.init.js') }}"></script>
    <script src="{{asset('js/jasny-bootstrap.js')}}"></script>
  <!-- input calendar thai -->
  <script src="{{ asset('plugins/components/bootstrap-datepicker-thai/js/bootstrap-datepicker.js') }}"></script>
  <!-- thai extension -->
  <script src="{{ asset('plugins/components/bootstrap-datepicker-thai/js/bootstrap-datepicker-thai.js') }}"></script>
  <script src="{{ asset('plugins/components/bootstrap-datepicker-thai/js/locales/bootstrap-datepicker.th.js') }}"></script>
  {{-- <script src="{{asset('plugins/components/sweet-alert2/sweetalert2.all.min.js')}}"></script> --}}
  <script src="{{asset('plugins/components/switchery/dist/switchery.min.js')}}"></script>
    <script type="text/javascript">

        $(document).ready(function () {
            // $('.select_all').prop('disabled', true);
            // $('.select_all').parent().removeClass('disabled');
            // $('.select_all').parent().css({"background-color": "rgb(238, 238, 238);","border-radius":"50%"});

            var confirm_status = '{{ !empty($agent->confirm_status) ?  $agent->confirm_status : null }}';
            if(confirm_status != null &&confirm_status != ''){
                $('.confirm_status').prop('disabled', true);
                $('.confirm_status').parent().removeClass('disabled');
                $('#personfile').prop('disabled', true);
            }

        $('#btn_submit').click(function (e) {
            var row =  $('.confirm_status:checked').val();
            var head_name = '{{ !empty($agent->head_name) ?  $agent->head_name : null }}';
            var title = '';
                if(row == 1){
                    title = 'ยันยัน';
                }else{
                    title = 'ยกเลิก';
                }
              Swal.fire({
                  title: 'ต้องการ'+title+'การรับมอบสิทธิ์จาก'+head_name+'หรือไม่?',
                  width: 600,
                  showDenyButton: true,
                  showCancelButton: true,
                  confirmButtonText: 'บันทึก',
                  cancelButtonText: 'ยกเลิก',
              }).then((result) => {

                    if (result.value) {
                        $('#from_box').submit();
                    }
              });
          });


            // $(".js-switch").each(function() {
            //     new Switchery($(this)[0], { size: 'small' });
            //  });


            //  select_all();
                // เงื่อนไขการมอบสิทธิ์
            // function select_all() {
            //    var row =  $('.select_all:checked').val();
            //    if(row == 2){
            //       $('#div_select_all').show();
            //    }else{
            //       $('#div_select_all').hide();
            //    }
            // }
        });
    </script>

@endpush
