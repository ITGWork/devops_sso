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
                        <i class="mdi mdi-book-open-page-variant"></i> คู่มือการใช้งาน
                    </h2>
                    <hr class="m-t-0" />
            
                    @if(count($manuals) > 0)
                        <div class="table-responsive">
                            <table class="table color-bordered-table primary-bordered-table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>ชื่อคู่มือ</th>
                                        <th><i class="mdi mdi-arrow-down-bold-circle font-20"></i> ดาวน์โหลด</th>
                                    </tr>
                                </thead>
                                <tbody>
            
                                    @foreach ($manuals as $key => $manual)
                                        <tr>
                                            <td class="col-md-1">{{ $key+1 }}</td>
                                            <td class="col-md-8">{{ $manual->title }}</td>
                                            <td class="col-md-3">
                                                <a href="{!! HP::getFileStorage($manual->file_url) !!}" target="_blank">
                                                    <i class="mdi mdi-arrow-down-bold-circle font-20"></i>
                                                    ดาวน์โหลด
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
            
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <span class="font-20"><i class="mdi mdi-information"></i> ไม่พบคู่มือการใช้งาน</span>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

@endsection

@push('js')

@endpush
