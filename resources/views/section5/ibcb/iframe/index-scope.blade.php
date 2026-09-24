@extends('layouts.app')

@push('css')
    <style>

    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                <center>
                    <h3>รายชื่อผู้ตรวจสอบการทำผลิตภัณฑ์อุตสาหกรรม</h3>
                </center>
                <center>
                    <h3>สาขาผลิตภัณฑ์อุตสาหกรรม</h3>
                </center>
                <br>

                @include('section5.ibcb.table.table-scope')

                @include('section5.ibcb.form.modal-cost-info')

            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {

        });
    </script>
@endpush
