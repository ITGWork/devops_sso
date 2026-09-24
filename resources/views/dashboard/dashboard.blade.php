@extends('layouts.master')

@push('css')

@endpush

@section('content')

  @php
    $session_id = session()->getId();
    $user       = auth()->user();
    $system     = !empty($user->system) ? explode(",", $user->system) : [];
    $setting_groups = App\Models\Setting\SettingSystemGroup::orderby('ordering')->get();

    $session    = App\Sessions::find($session_id);
    if(!is_null($session->act_instead)){//เข้าใช้ในฐานะผู้รับมอบอำนาจ
        $agent_systems = HP::getAgentSystems($user->id, $session->act_instead);//สิทธิ์ตามที่ได้รับมอบอำนาจ
        $agent_systems = $agent_systems->keys()->toArray();//ระบบที่ได้รับมอบอำนาจ
    }
  @endphp

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
			<div class="white-box">

                @foreach ($setting_groups as $key => $setting_group)

                    @php
                        $settings = App\Models\Setting\SettingSystem::where('group_id', $setting_group->id)
                                                                    ->where('state', 1)
                                                                    ->orderby('ordering')
                                                                    ->get();
                    @endphp

                    @if (count($settings) > 0)

                        <h3 class="box-title m-t-0">{{ $setting_group->title }}</h3>
                        <div class="row colorbox-group-widget">

                            @foreach ($settings as $setting)
                                @if (!is_null($setting->urls))

                                    @if(($setting_group->id==2 && $user->applicanttype_id < '4') || $setting_group->id==5 ){{-- users กลุ่ม 1,2,3 หรือ group 5 ต้องเข้าผ่าน i-Industry --}}

                                        <div class="col-md-4 col-sm-6 info-color-box">
                                            <a href="https://i-next.industry.go.th/">
                                                <div class="white-box ribbon-wrapper-reverse">

                                                    <div class="ribbon ribbon-corner ribbon-right ribbon-gray"><i class="fa fa-lock"></i></div>

                                                    <button type="button" class="media text-muted ribbon-content" >
                                                        <div class="media-body">
                                                            <h3 class="info-count">
                                                                <span class="pull-left text-muted">{{ $setting->title }} </span>
                                                                <br/>
                                                                <span class="pull-right" style="font-size:45px;"><i class="mdi {{ $setting->icons }}"></i></span>
                                                            </h3>
                                                            <p class="pull-left info-text font-12"> {{ $setting->details }} <i>(ต้องเข้าผ่าน i-Industry)</i></p>
                                                        </div>
                                                    </button>
                                                </div>
                                            </a>
					                    </div>
        <!-- bank -->

                                    @elseif(isset($agent_systems) && !in_array($setting->id, $agent_systems)){{-- เข้าในฐานะผู้รับมอบแต่ไม่มีสิทธิ์ --}}

                                        <div class="col-md-4 col-sm-6 info-color-box">
                                            <div class="white-box ribbon-wrapper-reverse">

                                                <div class="ribbon ribbon-corner ribbon-right ribbon-gray"><i class="fa fa-lock"></i></div>

                                                <button type="button" class="media text-muted ribbon-content" disabled>
                                                    <div class="media-body">
                                                        <h3 class="info-count">
                                                            <span class="pull-left text-muted">{{ $setting->title }} </span>
                                                            <br/>
                                                            <span class="pull-right" style="font-size:45px;"><i class="mdi {{ $setting->icons }}"></i></span>
                                                        </h3>
                                                        <p class="pull-left info-text font-12"> {{ $setting->details }} <i>(ไม่ได้รับมอบสิทธิ์)</i></p>
                                                    </div>
                                                </button>
                                            </div>
                                        </div>

                                    @else

                                        @if($user->branch_type==2 && $setting->branch_block==1){{-- เป็นสาขาและระบบไม่ให้สาขาเข้าใช้งาน --}}
                                            <div class="col-md-4 col-sm-6 info-color-box">
                                                <div class="white-box ribbon-wrapper-reverse">

                                                    <div class="ribbon ribbon-corner ribbon-right ribbon-gray"><i class="fa fa-lock"></i></div>

                                                    <button type="button" class="media text-muted ribbon-content" disabled>
                                                        <div class="media-body">
                                                            <h3 class="info-count">
                                                                <span class="pull-left text-muted">{{ $setting->title }} </span>
                                                                <br/>
                                                                <span class="pull-right" style="font-size:45px;"><i class="mdi {{ $setting->icons }}"></i></span>
                                                            </h3>
                                                            <p class="pull-left info-text font-12"> {{ $setting->details }}
                                                                <i>(สาขาไม่ได้รับอนุญาตให้ใช้งาน)</i>
                                                            </p>
                                                        </div>
                                                    </button>
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-md-4 col-sm-6 info-color-box waves-effect waves-light">
                                                <a href="{{ url('redirect/'.$setting->id) }}">
                                                    <div class="white-box">
                                                        <div class="media {{ $setting->colors }}">
                                                            <div class="media-body">
                                                                <h3 class="info-count"> {{ $setting->title }} <br/>
                                                                    <span class="pull-right" style="font-size:45px;"><i class="mdi {{ $setting->icons }}"></i></span>
                                                                </h3>
                                                                <p class="info-text font-12"> {{ $setting->details }} </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endif

                                    @endif

                                @endif

                            @endforeach

                        </div>
                    @endif
                @endforeach

                {{-- <div class="alert alert-info alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <b>แจ้งปิดปรับปรุงระบบ : </b>วันอาทิตย์ ที่ 20 พฤศจิกายน พ.ศ. 2565 เวลา 18.00 น. <u>ถึง</u> วันจันทร์ ที่ 21 พฤศจิกายน พ.ศ. 2565 เวลา 08.00 น.
                </div> --}}

            </div>
        </div>
    </div>
</div>

@endsection

@push('js')

    <script>
        $(document).ready(function () {

            @if(session()->has('flash_message'))
                Swal.fire({
                        position: 'center',
                        title: '{!! session()->get('flash_message') !!}',
                        showConfirmButton: true,
                        width: 800
                });
            @endif

        });
    </script>
@endpush
