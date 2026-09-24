@extends('layouts.master')

@push('css')
    <link rel="stylesheet" href="{{asset('plugins/components/toast-master/css/jquery.toast.css')}}">
@endpush

@section('content')

    @php
        $user = auth()->user();
        $picture = $user->picture == null ? asset('storage/uploads/users/no_avatar.jpg') : HP::getFileStorage('sso_users/'.$user->picture);
        $applicant_types = HP::applicant_types();
        $branch_types    = HP::branch_types();
    @endphp

<div class="container-fluid">

    <div class="row">
        <div class="col-md-4 col-xs-12">
            <div class="white-box">
                <div class="user-bg"> <img width="100%" alt="user" src="{{asset('plugins/images/large/factory.jpg')}}">
                    <div class="overlay-box">
                        <div class="user-content">

                            <a href="javascript:void(0)">
                                <img src="{{ $picture }}" class="thumb-lg img-circle" alt="img">
                            </a>

                            <h4 class="text-white">ชื่อผู้ใช้งาน</h4>
                            <h3 class="text-white">{{ $user->username }}</h3>
                        </div>
                    </div>
                </div>
                {{-- <div class="user-btm-box">
                    <div class="col-md-4 col-sm-4 text-center">
                        <p class="text-purple"><i class="ti-facebook"></i></p>
                        <h1>258</h1> </div>
                    <div class="col-md-4 col-sm-4 text-center">
                        <p class="text-blue"><i class="ti-twitter"></i></p>
                        <h1>125</h1> </div>
                    <div class="col-md-4 col-sm-4 text-center">
                        <p class="text-danger"><i class="ti-dribbble"></i></p>
                        <h1>556</h1> </div>
                </div> --}}
            </div>
        </div>
        <div class="col-md-8 col-xs-12">
            <div class="white-box">
                <ul class="nav nav-tabs tabs customtab">
                    {{-- <li class="tab">
                        <a href="#home" data-toggle="tab">
                            <span class="visible-xs"><i class="fa fa-home"></i></span>
                            <span class="hidden-xs">Activity</span>
                        </a>
                    </li> --}}
                    <li class="active tab">
                        <a href="#profile" data-toggle="tab">
                            <span class="visible-xs"><i class="fa fa-user"></i></span>
                            <span class="hidden-xs">โปรไฟล์</span>
                        </a>
                    </li>
                    {{-- <li class="tab">
                        <a href="#messages" data-toggle="tab" aria-expanded="true">
                            <span class="visible-xs"><i class="fa fa-envelope-o"></i></span>
                            <span class="hidden-xs">Messages</span>
                        </a>
                    </li> --}}
                </ul>

                <div class="tab-content m-t-0">
                    <div class="tab-pane" id="home">
                        <div class="steamline">
                            <div class="sl-item">
                                <div class="sl-left"> <img src="{{asset('plugins/images/users/1.jpg')}}" alt="user" class="img-circle" /> </div>
                                <div class="sl-right">
                                    <div class="m-l-40"><a href="#" class="text-info">John Doe</a> <span class="sl-date">5 minutes ago</span>
                                        <p>assign a new task <a href="#"> Design weblayout</a></p>
                                        <div class="m-t-20 row"><img src="{{asset('plugins/images/img1.jpg')}}" alt="user" class="col-md-3 col-xs-12" /> <img src="{{asset('plugins/images/img2.jpg')}}" alt="user" class="col-md-3 col-xs-12" /> <img src="{{asset('plugins/images/img3.jpg')}}" alt="user" class="col-md-3 col-xs-12" /></div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="sl-item">
                                <div class="sl-left"> <img src="{{asset('plugins/images/users/2.jpg')}}" alt="user" class="img-circle" /> </div>
                                <div class="sl-right">
                                    <div class="m-l-40"> <a href="#" class="text-info">John Doe</a> <span class="sl-date">5 minutes ago</span>
                                        <div class="m-t-20 row">
                                            <div class="col-md-2 col-xs-12"><img src="{{asset('plugins/images/img1.jpg')}}" alt="user" class="img-responsive" /></div>
                                            <div class="col-md-9 col-xs-12">
                                                <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi. Nulla quis sem at nibh elementum imperdiet. Duis sagittis ipsum. Praesent mauris. Fusce nec tellus sed augue semper porta. Mauris massa</p> <a href="#" class="btn btn-success"> Design weblayout</a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="sl-item">
                                <div class="sl-left"> <img src="{{asset('plugins/images/users/3.jpg')}}" alt="user" class="img-circle" /> </div>
                                <div class="sl-right">
                                    <div class="m-l-40"><a href="#" class="text-info">John Doe</a> <span class="sl-date">5 minutes ago</span>
                                        <p class="m-t-10"> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi. Nulla quis sem at nibh elementum imperdiet. Duis sagittis ipsum. Praesent mauris. Fusce nec tellus sed augue semper </p>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="sl-item">
                                <div class="sl-left"> <img src="{{asset('plugins/images/users/4.jpg')}}" alt="user" class="img-circle" /> </div>
                                <div class="sl-right">
                                    <div class="m-l-40"><a href="#" class="text-info">John Doe</a> <span class="sl-date">5 minutes ago</span>
                                        <p>assign a new task <a href="#"> Design weblayout</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane active" id="profile">
                        <div class="row">
                            <button type="button" class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#edit-modal" data-applicanttype_id="{{ $user->applicanttype_id }}">
                                <i class="fa fa-edit"></i> แก้ไข
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-3 col-xs-6 b-r"> <strong>ชื่อเต็ม</strong>
                                <br>
                                <p class="text-muted">{{ $user->name }}</p>
                            </div>
                            <div class="col-md-3 col-xs-6 b-r"> <strong>เบอร์โทร</strong>
                                <br>
                                <p class="text-muted">{{ $user->contact_tel }}</p>
                            </div>
                            <div class="col-md-3 col-xs-6 b-r"> <strong>อีเมล</strong>
                                <br>
                                <p class="text-muted">{{ $user->email }}</p>
                            </div>
                            <div class="col-md-3 col-xs-6"> <strong>ชื่อผู้ติดต่อ</strong>
                                <br>
                                <p class="text-muted">{{ @$user->contact_first_name.' '.@$user->contact_last_name }}</p>
                            </div>
                        </div>

                        <div class="form-horizontal" role="form">
                            <div class="form-body">
                                <h4 class="font-bold">ข้อมูลการลงทะเบียน</h4>
                                <hr class="m-t-0 m-b-10">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-3">ประเภทการลงทะเบียน:</label>
                                            <div class="col-md-9">
                                                <p class="form-control-static"> {{ array_key_exists($user->applicanttype_id, $applicant_types) ? $applicant_types[$user->applicanttype_id] : '-' }} </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-3">เลขประจำตัวผู้เสียภาษี:</label>
                                            <div class="col-md-9">
                                                <p class="form-control-static"> {{ $user->tax_number }} </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-3">วันที่จดทะเบียน:</label>
                                            <div class="col-md-9">
                                                <p class="form-control-static"> {{ !is_null($user->date_niti) ? HP::revertDate($user->date_niti) : '-' }} </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-3">ชื่อผู้ประกอบการ:</label>
                                            <div class="col-md-9">
                                                <p class="form-control-static"> {{ $user->name }} </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6 p-r-10">ประเภทสาขา:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ array_key_exists($user->branch_type, $branch_types) ? $branch_types[$user->branch_type] : '-' }} </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">รหัสสาขา:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ $user->branch_code }} </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h4 class="font-bold">ที่ตั้งสำนักงานใหญ่</h4>
                                <hr class="m-t-0 m-b-10">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-3 p-r-20">เลขที่:</label>
                                            <div class="col-md-9">
                                                <p class="form-control-static"> {{ $user->address_no }} </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">ตรอก/ซอย:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ $user->soi }} </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">หมู่:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ $user->moo }} </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">ถนน:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ $user->street }} </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">แขวง/ตำบล:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ $user->subdistrict }} </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">เขต/อำเภอ:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ $user->district }} </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">จังหวัด:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ $user->province }} </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">รหัสไปรษณีย์:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ $user->zipcode }} </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">พิกัดที่ตั้ง:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static">
                                                    @if(is_numeric($user->latitude) && is_numeric($user->longitude))
                                                        <a data-toggle="modal" data-target="#modal-map-show" style="cursor: pointer;">{{ $user->latitude.', '.$user->longitude }}</a>
                                                    @else
                                                        -
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h4 class="font-bold">ที่อยู่ที่สามารถติดต่อได้</h4>
                                <hr class="m-t-0 m-b-10">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">เลขที่:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ $user->contact_address_no }} </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">อาคาร/หมู่บ้าน:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ $user->contact_building }} </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">ตรอก/ซอย:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ $user->contact_soi }} </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">หมู่:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ $user->contact_moo }} </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">ถนน:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ $user->contact_street }} </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">แขวง/ตำบล:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ $user->contact_subdistrict }} </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">เขต/อำเภอ:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ $user->contact_district }} </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">จังหวัด:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ $user->contact_province }} </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">รหัสไปรษณีย์:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ $user->contact_zipcode }} </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h4 class="font-bold">ข้อมูลผู้ติดต่อ</h4>
                                <hr class="m-t-0 m-b-10">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">เลขประจำตัวประชาชน:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ $user->contact_tax_id }} </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">ชื่อผู้ติดต่อ:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ $user->ContactFullName }} </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">ตำแหน่ง:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ $user->contact_position }} </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">เบอร์โทรศัพท์:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ $user->contact_tel }} </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">เบอร์มือถือ:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ $user->contact_phone_number }} </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">โทรสาร:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ $user->contact_fax }} </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="control-label col-md-6">อีเมล:</label>
                                            <div class="col-md-6">
                                                <p class="form-control-static"> {{ $user->email }} </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        @include('profile.modal-edit')
                    </div>

                    <div class="tab-pane" id="messages">
                        <div class="steamline">
                            <div class="sl-item">
                                <div class="sl-left"> <img src="{{asset('plugins/images/users/1.jpg')}}" alt="user" class="img-circle" /> </div>
                                <div class="sl-right">
                                    <div class="m-l-40"> <a href="#" class="text-info">John Doe</a> <span class="sl-date">5 minutes ago</span>
                                        <div class="m-t-20 row">
                                            <div class="col-md-2 col-xs-12"><img src="{{asset('plugins/images/img1.jpg')}}" alt="user" class="img-responsive" /></div>
                                            <div class="col-md-9 col-xs-12">
                                                <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi. Nulla quis sem at nibh elementum imperdiet. Duis sagittis ipsum. Praesent mauris. Fusce nec tellus sed augue semper porta. Mauris massa</p> <a href="#" class="btn btn-success"> Design weblayout</a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="sl-item">
                                <div class="sl-left"> <img src="{{asset('plugins/images/users/2.jpg')}}" alt="user" class="img-circle" /> </div>
                                <div class="sl-right">
                                    <div class="m-l-40"><a href="#" class="text-info">John Doe</a> <span class="sl-date">5 minutes ago</span>
                                        <p>assign a new task <a href="#"> Design weblayout</a></p>
                                        <div class="m-t-20 row"><img src="{{asset('plugins/images/img1.jpg')}}" alt="user" class="col-md-3 col-xs-12" /> <img src="{{asset('plugins/images/img2.jpg')}}" alt="user" class="col-md-3 col-xs-12" /> <img src="{{asset('plugins/images/img3.jpg')}}" alt="user" class="col-md-3 col-xs-12" /></div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="sl-item">
                                <div class="sl-left"> <img src="{{asset('plugins/images/users/3.jpg')}}" alt="user" class="img-circle" /> </div>
                                <div class="sl-right">
                                    <div class="m-l-40"><a href="#" class="text-info">John Doe</a> <span class="sl-date">5 minutes ago</span>
                                        <p class="m-t-10"> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi. Nulla quis sem at nibh elementum imperdiet. Duis sagittis ipsum. Praesent mauris. Fusce nec tellus sed augue semper </p>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="sl-item">
                                <div class="sl-left"> <img src="{{asset('plugins/images/users/4.jpg')}}" alt="user" class="img-circle" /> </div>
                                <div class="sl-right">
                                    <div class="m-l-40"><a href="#" class="text-info">John Doe</a> <span class="sl-date">5 minutes ago</span>
                                        <p>assign a new task <a href="#"> Design weblayout</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-map-show">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"
                        data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times</span>
                </button>
            </div>
            <div class="modal-body">
                <style>
                    .controls {
                         margin-top: 10px;
                         border: 1px solid transparent;
                         border-radius: 2px 0 0 2px;
                         box-sizing: border-box;
                         -moz-box-sizing: border-box;
                         height: 32px;
                         outline: none;
                         box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
                    }
                </style>

                <div id="map" style="height: 400px;"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script src="{{asset('plugins/components/toast-master/js/jquery.toast.js')}}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAkwr5rmzY9btU08sQlU9N0qfmo8YmE91Y&libraries=places&callback=initAutocomplete" async defer></script>

    <script>
        // This example adds a search box to a map, using the Google Place Autocomplete
        // feature. People can enter geographical searches. The search box will return a
        // pick list containing a mix of places and predicted search terms.
        var markers   = [];
        var latitude  = {{ (int)$user->latitude }};
        var longitude = {{ (int)$user->longitude }};
        if(latitude===0 && longitude===0){
            latitude  = 13.765058723286717;
            longitude = 100.52727361839142;
        }

        function initAutocomplete() {
            var map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: latitude, lng: longitude },
                zoom: 15,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            markers = new google.maps.Marker({
                position: { lat: latitude, lng: longitude },
                map: map,
            });

        }

        @if(\Session::has('message'))
            $.toast({
                heading: 'Success!',
                position: 'top-center',
                text: '{{session()->get('message')}}',
                loaderBg: '#ff6849',
                icon: 'success',
                hideAfter: 3000,
                stack: 6
            });
        @endif

        // บังคับกรอกข้อมูลผู้ติดต่อ
        @if(\Session::has('required_contact'))
            $('#edit-modal').modal('show');//Show Modal Edit
            $('#alert-message').html('<div class="alert alert-primary"> กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน ก่อนทำธุรกรรมในระบบบริการอิเล็กทรอนิกส์ สมอ. </div>');//In Modal
        @endif

    </script>
@endpush
