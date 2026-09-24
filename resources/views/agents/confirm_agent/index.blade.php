@extends('layouts.master')

@push('css')
    <link href="{{asset('plugins/components/datatables/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet"
          type="text/css"/>
          <link href="{{asset('plugins/components/bootstrap-datepicker-thai/css/datepicker.css')}}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="container-fluid">
        <!-- .row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">รับมอบสิทธิ์เข้าใช้งานระบบ</h3>


                    <div class="clearfix"></div>
                    <hr>

                    {!! Form::model($filter, ['url' => '/confirm-agents', 'method' => 'get', 'class' => 'form-horizontal', 'id' => 'myFilter']) !!}

					  <div class="row">
                          <div class="col-md-5">
                            {!! Form::label('filter_search', 'search:', ['class' => 'col-md-2 control-label label-filter']) !!}
                            <div class="form-group col-md-10">
                                {!! Form::text('filter_search', null, ['class' => 'form-control', 'placeholder'=>'search']); !!}
                            </div>
                          </div>

                            <div class="col-md-2">
                                <div class="form-group  pull-left">
                                    <button type="submit" class="btn btn-info waves-effect waves-light" style="margin-bottom: -1px;">ค้นหา</button>
                                </div>

                                <div class="form-group  pull-left m-l-15">
                                    <button type="button" class="btn btn-warning waves-effect waves-light" id="filter_clear">
                                        ล้าง
                                    </button>
                                </div>
                            </div><!-- /.col-lg-1 -->
                            <div class="col-md-3">
                                {!! Form::label('filter_state', 'สถานะ', ['class' => 'col-md-2 control-label label-filter']) !!}
                                <div class="col-md-10">
                                     {!! Form::select('filter_state',
                                    ['1'=>'มอบสิทธิ์', '2'=>'ดำเนินการตามรับมอบ', '3'=>'สิ้นสุดการดำเนินการ', '4'=>'หมดอายุ','5'=>'ไม่ยืนยันการรับมอบ'],
                                     null,
                                     ['class' => 'form-control','id'=>'filter_state','placeholder'   => '-เลือกสถานะ-']); !!}
                                </div>
                             </div>
                            <div class="col-md-2">
                                {!! Form::label('perPage', 'Show', ['class' => 'col-md-4 control-label label-filter']) !!}
                                <div class="col-md-8">
                                     {!! Form::select('perPage',
                                    ['10'=>'10', '20'=>'20', '50'=>'50', '100'=>'100','500'=>'500'],
                                     null,
                                     ['class' => 'form-control']); !!}
                                </div>
                             </div>
                    </div>

					<input type="hidden" name="sort" value="{{ Request::get('sort') }}" />
					<input type="hidden" name="direction" value="{{ Request::get('direction') }}" />

					{!! Form::close() !!}

                    <div class="clearfix"></div>

                    <div class="table-responsive">



                    <div class="table-responsive">
                        <table class="table table-borderless"  >
                            <thead>
                            <tr>
                                <th width="2%" class="text-center">ลำดับ</th>
                                <th width="25%"  class="text-center">@sortablelink('head_name', 'ผู้มอบสิทธิ์')</th>
                                <th width="15%" class="text-center">@sortablelink('created_at', 'วันที่มอบสิทธิ์')</th>
                                <th width="15%" class="text-center">@sortablelink('confirm_date', 'วันที่ยืนยันการมอบ')</th>
                                <th width="15%" class="text-center">ระบบที่มอบสิทธิ์</th>
                                <th width="15%" class="text-center">@sortablelink('select_all', 'เงื่อนไข')</th>
                                <th width="10%" class="text-center">@sortablelink('state', 'สถานะ')</th>
                                <th width="8%" class="text-center">ยืนยัน</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($agent as $item)
                                @php
                                    $user_head = $item->user_head_created; //ผู้มอบสิทธิ์
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $loop->iteration or $item->id }}</td>
                                    <td>
                                        @if(!is_null($user_head))
                                            {{ $user_head->name }}
                                            @if($user_head->applicanttype_id!=2) {{-- ไม่ใช่บุคคลธรรมดา --}}
                                                @if($user_head->branch_type==1)
                                                    <br>(<span class="text-primary">สำนักงานใหญ่</span>)
                                                @elseif($user_head->branch_type==2)
                                                    <br>(<span class="text-info">รหัสสาขา {{ $user_head->branch_code }}</span>)
                                                @endif
                                            @endif
                                            <br>
                                            ({!! !empty($user_head->tax_number) ? $user_head->tax_number : '<i class="text-muted">ไม่มีเลขผู้เสียภาษี</i>' !!})
                                        @else
                                            <i class="text-muted">ไม่มีข้อมูลใช้งานในระบบ</i>
                                        @endif
                                        {{-- {!! !empty($user_head) ? $item->head_name : null !!} <br>
                                        {!! !empty($item->user_taxid) ? '('.$item->user_taxid.')' : null !!} --}}
                                    </td>
                                    <td>{{ !empty($item->created_at) ? HP::DateTimeThai($item->created_at) : null  }}</td>
                                    <td>{{ !empty($item->confirm_date) ? HP::DateTimeThai($item->confirm_date) : null  }}</td>
                                    <td>{!! !empty($item->AgentSystem) ? $item->AgentSystem : null  !!}</td>
                                    <td>

                                        @if ($item->issue_type == 1)
                                            ตลอดเวลา
                                        @elseif ($item->issue_type == 2)
                                            {{ !empty($item->start_date)  &&  !empty($item->end_date)  ? 'ช่วงวันที่' .HP::DateThai($item->start_date).' - '.HP::DateThai($item->end_date) : null}}
                                        @else
                                             -
                                        @endif
                                    </td>
                                    <td>
                                        {{ !empty($item->StateText) ? $item->StateText : null  }}
                                        @if ( !empty($user_head->block) && $user_head->block == 1)
                                          <p class="text-warning">ผู้มอบสิทธิ์ถูกปิดใช้งานบัญชี</p>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($item->state == 1)
                                            <a href="{{ url('/confirm-agents/' . $item->id . '/edit') }}" title="แก้ไข">
                                                <button class="btn btn-primary btn-sm">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"> </i>
                                                </button>
                                            </a>

                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="pagination-wrapper"> {!! $agent->appends(['search' => Request::get('search')])->render() !!} </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection



@push('js')
    <script src="{{asset('plugins/components/toast-master/js/jquery.toast.js')}}"></script>

    <script src="{{asset('plugins/components/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/components/toast-master/js/jquery.toast.js')}}"></script>
    <!-- input calendar thai -->
    <script src="{{ asset('plugins/components/bootstrap-datepicker-thai/js/bootstrap-datepicker.js') }}"></script>
    <!-- thai extension -->
    <script src="{{ asset('plugins/components/bootstrap-datepicker-thai/js/bootstrap-datepicker-thai.js') }}"></script>
    <script src="{{ asset('plugins/components/bootstrap-datepicker-thai/js/locales/bootstrap-datepicker.th.js') }}"></script>
    <!-- start - This is for export functionality only -->
    <!-- end - This is for export functionality only -->
    <script>
        $(document).ready(function () {
            $( "#filter_clear" ).click(function() {
                $('#perPage').val('');
                $('#filter_search').val('');
                window.location.assign("{{url('/confirm-agents')}}");
            });
            @if(\Session::has('flash_message'))
            $.toast({
                heading: 'Success!',
                position: 'top-center',
                text: '{{session()->get('flash_message')}}',
                loaderBg: '#33ff33',
                icon: 'success',
                hideAfter: 3000,
                stack: 6
            });
            @endif

            @if(\Session::has('message_error'))
                $.toast({
                    heading: 'Error!',
                    position: 'top-center',
                    text: '{{session()->get('message_error')}}',
                    loaderBg: '#ff6849',
                    icon: 'error',
                    hideAfter: 3000,
                    stack: 6
                });
            @endif
        })

        $(function () {
            $('#myTable').DataTable( {
                    dom: 'Brtip',
                    pageLength:5,
                    processing: true,
                    lengthChange: false,
                    ordering: false,
                    order: [[ 0, "desc" ]]
                });

        });
    </script>

@endpush
