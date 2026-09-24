@extends('layouts.master')

@push('css')

@endpush

@section('content')
 
  @php
    $session_id = session()->getId();
    $user           = auth()->user();
    $system         = !empty($user->system) ? explode(",",$user->system) : [];
    $setting_url1   = App\Models\Setting\SettingUrl::select('data')->where('column_name','url_e_license')->first();
    $setting_url2   = App\Models\Setting\SettingUrl::select('data')->where('column_name','url_nsw')->first();
    $setting_url3   = App\Models\Setting\SettingUrl::select('data')->where('column_name','url_e_accreditation')->first();
    $setting_url4   = App\Models\Setting\SettingUrl::select('data')->where('column_name','url_e_certify')->first();
  @endphp
 
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
               
                <div class="row colorbox-group-widget">
                    @if (in_array('1',$system) && !is_null($setting_url1))
                      <div class="col-md-4 col-sm-6 info-color-box waves-effect waves-light">
                        <form id="myform" name="myform" action="{{$setting_url1->data}}"   method="POST" enctype="multipart/form-data" class="form-horizontal">
 
                                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                                <input type="hidden" name="session_id" value="{{ $session_id }}"/>
                                <input type="hidden" name="user_agent" value="{{ $_SERVER['HTTP_USER_AGENT'] }}"/>
                                
                          <div class="white-box">
                            <button type="submit" id="e_accreditation"  class="media btn btn-success">
                                  <div class="media-body">
                                      <h3 class="info-count">
                                          <span class="pull-left ">  e-License </span>
                                          <br/>
                                        <span class="pull-right" style="font-size:45px;"><i class="mdi mdi-wunderlist"></i></span>
                                      </h3>
                                      <p class="pull-left  info-text font-12">ระบบออกใบอนุญาต</p>
                                  </div>
                              </button>
                          </div>
                        </form>
                      </div>
                    @endif
                    @if (in_array('2',$system)  && !is_null($setting_url2))
                    <div class="col-md-4 col-sm-6 info-color-box waves-effect waves-light">
                        <form action="{{$setting_url2->data}}"   method="POST"   enctype="application/x-www-form-urlencoded" > 

                            <input type="hidden" name="session_id" value="{{ $session_id }}"/>
                            <input type="hidden" name="user_agent" value="{{ $_SERVER['HTTP_USER_AGENT'] }}"/>
                            <div class="white-box">
                                <button   type="submit"  class="media btn btn-warning">
                                    <div class="media-body">
                                        <h3 class="info-count">
                                            <span class="pull-left ">NSW</span>
                                            <br/>
                                        <span class="pull-right" style="font-size:45px;"><i class="mdi mdi-ferry"></i></span>
                                        </h3>
                                        <p class="pull-left info-text font-12">ระบบ NSW</p>
                                    </div>
                                </button>
                            </div>
                         </form>  
                    </div>
                    @endif
                    @if (in_array('3',$system)  && !is_null($setting_url3))
                    <div class="col-md-4 col-sm-6 info-color-box waves-effect waves-light">
                        <form action="{{$setting_url3->data}}"   method="POST"   enctype="application/x-www-form-urlencoded" > 

                            <input type="hidden" name="session_id" value="{{ $session_id }}"/>
                            <input type="hidden" name="user_agent" value="{{ $_SERVER['HTTP_USER_AGENT'] }}"/>
                            <div class="white-box">
                                <button   type="submit"  class="media btn btn-info">
                                    <div class="media-body">
                                        <h3 class="info-count">
                                            <span class="pull-left ">e-Surveillance</span>
                                            <br/>
                                        <span class="pull-right" style="font-size:45px;"><i class="mdi mdi-clipboard-text"></i></span>
                                        </h3>
                                        <p class="pull-left info-text font-12">ระบบตรวจติดตามออนไลน์</p>
                                    </div>
                                </button>
                            </div>
                         </form>  
                    </div>
                    @endif
                    @if (in_array('3',$system)  && !is_null($setting_url4))
                    <div class="col-md-4 col-sm-6 info-color-box waves-effect waves-light">
                      <form action="{{$setting_url4->data}}"   method="POST"    > 

                                 <input type="hidden" name="session_id" value="{{ $session_id }}"/>
                                 <input type="hidden" name="user_agent" value="{{ $_SERVER['HTTP_USER_AGENT'] }}"/>
                          <div class="white-box">
                                <button type="submit" id="e_accreditation"  class="media btn btn-primary">
                                    <div class="media-body">
                                        <h3 class="info-count">
                                            <span class="pull-left "> รับรองระบบงาน </span>
                                            <br/>
                                            <span class="pull-right" style="font-size:45px;"><i class="mdi mdi-certificate"></i></span>
                                        </h3>
                                        <p class="pull-left info-text font-12">ระบบการรับรองระบบงาน</p>
                                    </div>
                                </button>
                          </div>
                      </form>  
                    </div>
                    @endif
                </div>
          
            </div>
         </div>
    </div>
</div>
@endsection

@push('js')
 
    <script src='{{asset('plugins/components/moment/moment.js')}}'></script>
    <script src='{{asset('plugins/components/fullcalendar/fullcalendar.js')}}'></script>
    {{-- <script src='{{asset('js/db2.js')}}'></script> --}}

 <script type='text/javascript'>
     function submit()
      {
         document.forms["myform"].submit();
      }
 
</script>

@endpush


 