@extends('layouts.master')

@section('title', ' เพิ่ม/ลดขอบข่าย')

@push('css')
    <link href="{{asset('plugins/components/datatables/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{asset('plugins/components/toast-master/css/jquery.toast.css')}}">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">
                        เพิ่ม/ลดขอบข่าย
                    </h3>

                    <div class="clearfix"></div>
                    <hr>

                    <div class="row box_filter">
                        <div class="col-md-6">
                            {!! Form::label('filter_search', 'คำค้นหา:', ['class' => 'col-md-2 control-label label-filter']) !!}
                            <div class="form-group col-md-10">
                                {!! Form::text('filter_search', null, ['class' => 'form-control', 'placeholder' => 'ค้นหา']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="clearfix"></div>
                    <hr>

                    <div class="table-responsive">
                        <table class="table table-borderless" id="myTable">
                            <thead>
                                <tr>
                                    <th width="1%" class="text-center">รหัส IB</th>
                                    <th class="text-center">ห้องปฏิบัติการ</th>
                                    <th class="text-center">เลขนิติบุคคล</th>
                                    <th class="text-center">หมวดอุตสาหกรรม/สาขา</th>
                                    <th class="text-center">ประเภท</th>
                                    <th class="text-center">วันที่เป็นหน่วยตรวจสอบ</th>
                                    <th class="text-center">สถานะ</th>
                                    <th class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{asset('plugins/components/toast-master/js/jquery.toast.js')}}"></script>

    <script src="{{asset('plugins/components/datatables/jquery.dataTables.min.js')}}"></script>

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
                    // html: '<p class="h4"></p>',
                    width: 500
                });
            @endif

            @if(\Session::has('flash_message_delete'))
                Swal.fire({
                    type: 'success',
                    title: 'ลบคำขอเรียบร้อย',
                    // html: '<p class="h4"></p>',
                    width: 500
                });
            @endif


        });
    </script>
@endpush