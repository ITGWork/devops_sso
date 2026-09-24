@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
                <h3 class="box-title pull-left">มอบสิทธิ์เข้าใช้งานระบบ #{{ $agent->id }}</h3>
                    <a class="btn btn-success pull-right" href="{{ url('/agents') }}">
                        <i class="icon-arrow-left-circle" aria-hidden="true"></i>กลับ</a>

                <div class="clearfix"></div>
                <hr>

                {!! Form::model($agent, [
                    'method' => 'PATCH',
                    'url' => ['/agents', $agent->id],
                    'class' => 'form-horizontal',
                    'files' => true
                ]) !!}

                <div id="box-readonly">
                    @include ('agents.agent.form', ['submitButtonText' => 'Update'])
                </div>
  
                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>
@endsection

@push('js')

    <script>
        jQuery(document).ready(function() {

            $('#box-readonly').find('button[type="submit"]').remove();
            $('#box-readonly').find('.icon-close').parent().remove();
            $('#box-readonly').find('.fa-copy').parent().remove();
            $('#box-readonly').find('input').prop('disabled', true);
            $('#box-readonly').find('textarea').prop('disabled', true);
            $('#box-readonly').find('select').prop('disabled', true);
            $('#box-readonly').find('.bootstrap-tagsinput').prop('disabled', true);
            $('#box-readonly').find('span.tag').children('span[data-role="remove"]').remove();
            $('#box-readonly').find('button').prop('disabled', true);
            $('#box-readonly').find('button').remove();
            $('#box-readonly').find('.btn-remove-file').parent().remove();
            $('#box-readonly').find('.show_tag_a').hide();
            $('#box-readonly').find('.input_show_file').hide();
            
        });
    </script>

@endpush