@extends('layouts.welcome')

@section('content')


<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
                
                <?php
                    $config = HP::getConfig();
                ?>
                @if(property_exists($config, 'info_contact'))
                    {!! $config->info_contact !!}
                @else
                    <h3><i class="text-muted">ไม่มีข้อมูล</i></h3>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')

@endpush
