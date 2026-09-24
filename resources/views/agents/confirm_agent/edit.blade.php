@extends('layouts.master')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">รับมอบสิทธิ์เข้าใช้งานระบบ</h3>
                        <a class="btn btn-success pull-right" href="{{  app('url')->previous() }}">
                            <i class="icon-arrow-left-circle" aria-hidden="true"></i> กลับ</a>
      
                    <div class="clearfix"></div>
                    <hr>

                    @if ($errors->any())
                        <ul class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif

                    {!! Form::model($agent, [
                        'method' => 'PATCH',
                        'url' => ['/confirm-agents', $agent->id],
                        'class' => 'form-horizontal',
                        'files' => true,
                        'id'    => 'from_box'
                    ]) !!}
                        <div id="readonly-form">
                            @include ('agents.agent.form')
                        </div>
                            @include ('agents.confirm_agent.form')
                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
@push('js')
    <script type="text/javascript">
        $(document).ready(function () {
             //ควบคุมฟอร์มที่มาจากหน้าบันทึกของเจ้าหน้าที่สอบ
                $('#readonly-form').find('button, .link-cancel').remove();
                $('#readonly-form').find('textarea, input, select').prop('disabled', true);
                $('#readonly-form').find('.show_tag_a').remove();
                $('#readonly-form').find('#div_attach').remove();
        }); 
   </script>
   
@endpush
@endsection

