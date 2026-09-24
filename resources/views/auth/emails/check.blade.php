@extends('layouts.app')

@section('content')

    <style>
        .input-login{
            border-bottom: 1px solid black !important;
        }

        /*
	Max width before this PARTICULAR table gets nasty. This query will take effect for any screen smaller than 760px and also iPads specifically.
	*/
	@media
	  only screen
      and (max-width: 760px), (min-device-width: 768px)
      and (max-device-width: 1024px)  {

		/* Force table to not be like tables anymore */
		table, thead, tbody, th, td, tr {
			display: block;
		}

		/* Hide table headers (but not display: none;, for accessibility) */
		thead tr {
			position: absolute;
			top: -9999px;
			left: -9999px;
		}

        tr {
          margin: 0 0 1rem 0;
        }

        tr:nth-child(odd) {
          background: #eee;
        }

		td {
			/* Behave  like a "row" */
			border: none;
			border-bottom: 1px solid #eee;
			position: relative;
			padding-left: 50%;
		}

		td:before {
			/* Now like a table header */
			/*position: absolute;*/
			/* Top/left values mimic padding */
			top: 0;
			left: 6px;
			width: 45%;
			white-space: nowrap;
		}

		/*
		Label the data
        You could also use a data-* attribute and content for this. That way "bloats" the HTML, this way means you need to keep HTML and CSS in sync. Lea Verou has a clever way to handle with text-shadow.
		*/
		td:nth-of-type(1):before { content: "# :"; }
		td:nth-of-type(2):before { content: "ชื่อ :"; }
		td:nth-of-type(3):before { content: "สาขา :"; }
		td:nth-of-type(4):before { content: "อีเมล :"; }

	   }
    </style>

    <section id="wrapper" class="login-register">
        <div class="login-box m-t-30">
            <div class="white-box">
                <form class="form-horizontal form-material" method="get" action="{{ url('check-email') }}">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="form-group ">
                        <div class="col-xs-12">
                            <h3>ตรวจสอบอีเมลที่ได้ลงทะเบียนไว้</h3>
                            <p class="text-muted font-12">กรอกเลขผู้เสียภาษี/เลขประจำตัวประชาชน/เลขที่หนังสือเดินทาง</p>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="col-xs-12">
                            <input id="tax" value="{{ $tax }}" placeholder="กรอกเลขผู้เสียภาษี/ประจำตัวประชาชน/หนังสือเดินทาง" type="text" class="form-control {{ $errors->has('tax') ? ' is-invalid' : '' }} input-login" name="tax" value="{{ old('tax') }}" required>
                            @if ($errors->has('tax'))
                                <span class="invalid-feedback">
                                    <span class="text-danger">{{ $errors->first('tax') }}</span>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group text-center m-t-20">
                        <div class="col-xs-6 p-r-0">
                            <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">
                                ตกลง
                            </button>
                        </div>
                        <div class="col-xs-6">
                            <a href="{{ url('login') }}" class="btn btn-default btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">
                                ยกเลิก
                            </a>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        @if(!is_null($tax))
            <div class="row">
                <div class="col-xs-3"></div>

                <div class="col-md-6 col-xs-12">
                    <div class="white-box">
                        @if(count($user_list) > 0)
                            <div class="table-responsive">
                                <h5>พบข้อมูลดังต่อไปนี้
                                    <span class="pull-right m-b-10"><a class="btn btn-success" href="{{ url('login') }}"> <i class="fa fa-sign-in"></i> หน้าเข้าสู่ระบบ</a></span>
                                </h5>
                                <div class="clearfix"></div>
                                <table class="table color-bordered-table primary-bordered-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>ชื่อ</th>
                                            <th>สาขา</th>
                                            <th>อีเมล</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($user_list as $key => $user_item)
                                            <tr>
                                                <td> {{ $key+1 }}</td>
                                                <td>
                                                    {{ $user_item->name }}
                                                </td>
                                                <td>
                                                    @if($user_item->applicanttype_id!=2) {{-- ไม่ใช่บุคคลธรรมดา --}}
                                                        @if($user_item->branch_type==1)
                                                            สำนักงานใหญ่
                                                        @elseif($user_item->branch_type==2)
                                                            รหัสสาขา {{ $user_item->branch_code }}
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ HP::blur_email($user_item->email) }}
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-danger m-b-0"> ไม่พบข้อมูลที่ลงทะเบียนไว้ </div>
                        @endif
                    </div>
                </div>
            </div>

        @endif

    </section>
@endsection

@push('js')

@endpush
