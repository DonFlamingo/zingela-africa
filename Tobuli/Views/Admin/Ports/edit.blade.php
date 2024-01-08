@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon edit"></i> {{ trans('global.edit') }} "{{ $item->name }}" {{ strtolower(trans('validation.attributes.port')) }}
@stop

@section('body')
    {!! Form::open(array('url' => route('admin.ports.update', $item->name), 'method' => 'PUT')) !!}
    <div class="form-group">
        <div class="checkbox">
            {!! Form::checkbox('active', 1, $item->active) !!}
            {!! Form::label('active', trans('validation.attributes.active')) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('name', trans('validation.attributes.name').':') !!}
        {!! Form::text('name', $item->name, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('port', trans('validation.attributes.port').':') !!}
        {!! Form::text('port', $item->port, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('name', trans('validation.attributes.extra').':') !!}
        @foreach(json_decode($item->extra, TRUE) as $name => $value)
            <div class="row row-padding" style="padding-bottom:10px">
                <div class="col-xs-6">
                    {!! Form::text("extra[{$name}][name]", $name, ['class' => 'form-control']) !!}
                </div>
                <div class="col-xs-6">
                    <div class="input-group">
                        {!! Form::text("extra[{$name}][value]", $value, ['class' => 'form-control']) !!}
                        <span class="input-group-addon"><a href="javascript:" class="delete-extra-item remove-icon"><span aria-hidden="true">×</span></a></span>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="row row-padding extra-empty" style="padding-bottom:10px">
            <div class="col-xs-6">
                {!! Form::text("extra[empty][name]", null, ['class' => 'form-control']) !!}
            </div>
            <div class="col-xs-6">
                <div class="input-group">
                    {!! Form::text("extra[empty][value]", null, ['class' => 'form-control']) !!}
                    <span class="input-group-addon"><a href="javascript:" class="delete-extra-item remove-icon"><span aria-hidden="true">×</span></a></span>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@stop