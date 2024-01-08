@extends('Frontend.Layouts.modal')

@section('title')
    {{ trans('global.add_new') }}
@stop

@section('body')
    <ul class="nav nav-tabs nav-default" role="tablist">
        <li class="active"><a href="#plan-add-form-main" role="tab" data-toggle="tab">{{ trans('front.main') }}</a></li>
        <li><a href="#plan-add-form-permissions" role="tab" data-toggle="tab">{{ trans('validation.attributes.permissions') }}</a></li>
    </ul>
    {!! Form::open(array('route' => 'admin.billing.plan_store', 'method' => 'POST')) !!}
    {!! Form::hidden('id') !!}
    <div class="tab-content">
        <div id="plan-add-form-main" class="tab-pane active">
            <div class="form-group">
                {!! Form::label('title', trans('validation.attributes.title').':') !!}
                {!! Form::text('title', null, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('price', trans('validation.attributes.price').':') !!}
                {!! Form::text('price', null, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('objects', trans('validation.attributes.objects').':') !!}
                {!! Form::text('objects', null, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('duration_value', trans('validation.attributes.duration_value').':') !!}
                <div class="row">
                    <div class="col-md-6">
                        {!! Form::text('duration_value', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="col-md-6">
                        {!! Form::select('duration_type', $duration_types, null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div id="plan-add-form-permissions" class="tab-pane">
            @include('Admin.Clients._perms')
        </div>
    </div>
    {!! Form::close() !!}
    <script>
        checkPerms();
    </script>
@stop