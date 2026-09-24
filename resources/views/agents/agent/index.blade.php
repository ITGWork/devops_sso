@extends('layouts.master')

@push('css')
    <link href="{{asset('plugins/components/datatables/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{asset('plugins/components/toast-master/css/jquery.toast.css')}}">

@endpush

@section('content')
    <div class="container-fluid">
        <!-- .row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">มอบสิทธิ์เข้าใช้งานระบบ</h3>

                    <a class="btn btn-success pull-right" href="{{ url('/agents/create') }}"><i class="icon-plus"></i>เพิ่มผู้รับมอบสิทธิ์</a>

                    <div class="clearfix"></div>
                    <hr>

                    {!! Form::model($filter, ['url' => '/agents', 'method' => 'get', 'class' => 'form-horizontal', 'id' => 'myFilter']) !!}

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
                  <hr>

                  @include ('agents.agent.modals')

                    <div class="table-responsive">
                        <table class="table table-borderless" id="myTable">
                            <thead>
                                <tr>
                                    <th width="" class="text-center">ลำดับ</th>
                                    <th width="20%"  class="text-center">@sortablelink('agent_name', 'ผู้รับมอบสิทธิ์')</th>
                                    <th width="14%" class="text-center">@sortablelink('created_at', 'วันที่มอบสิทธิ์')</th>
                                    <th width="14%" class="text-center">@sortablelink('confirm_date', 'วันที่ยืนยันการมอบ')</th>
                                    <th width="15%" class="text-center">ระบบที่มอบสิทธิ์</th>
                                    <th width="21%" class="text-center">@sortablelink('select_all', 'เงื่อนไข')</th>
                                    <th width="15%" class="text-center">@sortablelink('state', 'สถานะ')</th>
                                    <th width="" class="text-center">ยืนยัน</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($agent as $item)
                                    <tr>
                                        <td>{{ $loop->iteration or $item->id }}</td>
                                        <td>
                                            {!! !empty($item->agent_name) ? $item->agent_name : null !!} <br>
                                            ( {!! !empty($item->agent_taxid) ? $item->agent_taxid : null !!} )
                                        </td>
                                        <td>{{ !empty($item->created_at) ? HP::DateTimeThai($item->created_at) : null  }}</td>
                                        <td>{{ !empty($item->confirm_date) ? HP::DateTimeThai($item->confirm_date) : '-'  }}</td>
                                        <td>{!! !empty($item->AgentSystem) ? $item->AgentSystem : null  !!}</td>
                                        <td>
                                            @if ($item->issue_type == 1)
                                                ตลอดเวลา
                                            @else
                                                {{ !empty($item->start_date)  &&  !empty($item->end_date)  ? 'ช่วงวันที่' .HP::DateThai($item->start_date).' - '.HP::DateThai($item->end_date) : null}}
                                            @endif
                                        </td>
                                        <td>{{ !empty($item->StateText) ? $item->StateText : null  }}</td>
                                        <td>
                                            <div class="button-box">
                                                <a href="{{ url('/agents/' . $item->id) }}" title="View Authorize">
                                                    <button class="btn btn-twitter waves-effect waves-light">
                                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                                    </button>
                                                </a>

                                                @if( $item->state == 2 )
                                                    {!! Form::button('<i class="fa fa-times" aria-hidden="true"></i>',
                                                        [ 'type' => 'button',
                                                            'class' => 'btn btn-googleplus waves-effect waves-light btn_delete',
                                                            'title' => 'Delete Authorize',
                                                            'data-id' => $item->id,
                                                            'data-agent_name' => $item->agent_name,
                                                            'data-agent_taxid' => $item->agent_taxid,
                                                            'data-created_at' => (!empty($item->created_at) ? HP::DateTimeThai($item->created_at) : null),
                                                        ]
                                                    ) !!}
                                                @elseif( $item->state == 1)
                                                    {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i>',
                                                        [ 'type' => 'button',
                                                            'class' => 'btn btn-googleplus waves-effect waves-light btn_delete',
                                                            'title' => 'Delete Authorize',
                                                            'data-id' => $item->id,
                                                            'data-agent_name' => $item->agent_name,
                                                            'data-agent_taxid' => $item->agent_taxid,
                                                            'data-created_at' => (!empty($item->created_at) ? HP::DateTimeThai($item->created_at) : null),
                                                        ]
                                                    ) !!}
                                                @endif



                                            {{-- <a href="{{ url('/agents/' . $item->id . '/edit') }}"title="Edit Authorize">
                                                <button class="btn btn-primary btn-sm">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"> </i> Edit
                                                </button>
                                            </a> --}}

                                            {{-- {!! Form::open(['method'=>'DELETE', 'url' => ['/agents', $item->id],'style' => 'display:inline']) !!}

                                                {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i> Delete', array(
                                                        'type' => 'submit',
                                                        'class' => 'btn btn-danger btn-sm',
                                                        'title' => 'Delete Authorize',
                                                        'onclick'=>'return confirm("Confirm delete?")'
                                                )) !!}

                                            {!! Form::close() !!} --}}

                                            {{-- <button 'type' = 'button', 'class' = 'btn btn-danger btn-sm btn_delete', 'title' = 'Delete Authorize','onclick'='return confirm("Confirm delete?")']'data-id' => $item->id><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button> --}}

                                            </div>
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
    {{-- <script src="{{asset('plugins/components/sweet-alert2/sweetalert2.all.min.js')}}"></script> --}}
    <!-- start - This is for export functionality only -->
    <!-- end - This is for export functionality only -->
    <script>
        $(document).ready(function () {

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

            @if(\Session::has('flash_message'))
                Swal.fire({
                    type: 'success',
                    title: 'บันทึกเรียบร้อย',
                    html: '<p class="h4">การมอบสิทธิ์นี้จะมีผลก็ต่อเมื่อผู้รับมอบยืนยันการรับมอบสิทธิ์</p>',
                    width: 500
                });
            @endif


        })

        $(function () {



            $('body').on('click', '#btn_delete_save', function(){

                var id = $('#modal_delete_id').val();
                var remarks_delete = $('#remarks_delete').val();


                if( id != '' && remarks_delete != ''){

                    if( confirm("Confirm delete?") ){

                        $.ajax({
                            method: "POST",
                            url: "{{ url('/agents/delete_update') }}",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "id": id,
                                "remarks_delete": remarks_delete
                            },
                            success : function (msg){
                                if (msg == "success") {

                                    $.toast({
                                        heading: 'Compleate!',
                                        text: 'บันทึกสำเร็จ',
                                        position: 'top-right',
                                        loaderBg: '#ff6849',
                                        icon: 'success',
                                        hideAfter: 1000,
                                        stack: 6
                                    });

                                    setTimeout(function(){
                                        $('#myFilter').submit();
                                    }, 1000);

                                }else{

                                    $.toast({
                                        heading: 'Compleate!',
                                        text: 'บันทึกไม่สำเร็จ',
                                        position: 'top-right',
                                        loaderBg: '#ff6849',
                                        icon: 'error',
                                        hideAfter: 1000,
                                        stack: 6
                                    });

                                }
                            }
                        });
                    }

                }else{
                    alert('กรุณาระบุหมายเหตุ');
                }

            });

            $('body').on('click', '.btn_delete', function(){

                var id = $(this).data('id');
                var agent_name = $(this).data('agent_name');
                var agent_taxid = $(this).data('agent_taxid');
                var created_at = $(this).data('created_at');

                $('.input_show').val('');

                $('#myModalLabel').text('ยกเลิกข้อมูลมอบสิทธิ์การเข้าใช้งานระบบ #');
                $('.agent_name_txt').text('');
                $('.agent_taxid_txt').text('');
                $('.created_at_txt').text('');

                if( id != ''){

                    $('#myModalLabel').text('ยกเลิกข้อมูลมอบสิทธิ์การเข้าใช้งานระบบ #'+id);
                    $('.agent_name_txt').text(agent_name);
                    $('.agent_taxid_txt').text(agent_taxid);
                    $('.created_at_txt').text(created_at);
                    $('#modal_delete_id').val(id);
                    $('#myModalDelete').modal('show');

                }

            });

            $('#filter_clear').click(function (e) {
                $('#filter_state').val('').trigger('change');
                $('#filter_search').val('');
            });

            $('#filter_state').change(function (e) {

                $('#myFilter').submit();

            });
        });
    </script>

@endpush
