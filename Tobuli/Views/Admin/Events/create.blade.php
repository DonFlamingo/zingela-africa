{{-- TODO empty-input-add --}}
@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon event-add"></i> {{ trans('global.add_new') }}
@stop

@section('body')
    {!!Form::open(['route' => 'admin.events.store', 'method' => 'POST'])!!}
    {!!Form::hidden('id')!!}
    <div class="form-group">
        <div class="checkbox">
            {!!Form::checkbox('always', 1, 0)!!}
            {!!Form::label(null, trans('admin.show_always'))!!}
        </div>
    </div>

    <div class="form-group">
        {!!Form::label('protocol', trans('validation.attributes.device_protocol').':')!!}
        {!!Form::select('protocol', $protocols, null, ['class' => 'form-control', 'data-live-search' => true])!!}
    </div>

    <div class="form-group">
        {!!Form::label('tag_value', trans('front.conditions').':')!!}
        {!!Form::hidden('conditions')!!}
        <div class="empty-input-items">
            <div class="form-group empty-input-add-new">
                <div class="row">
                    <div class="col-md-4 col-xs-4">
                        {!!Form::text('tag[]', null, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.parameter')])!!}
                    </div>
                    <div class="col-md-4 col-xs-4">
                        {!!Form::select('type[]', $types, null, ['class' => 'form-control'])!!}
                    </div>
                    <div class="col-md-4 col-xs-4">
                        <div class="input-group">
                            {!!Form::text('tag_value[]', null, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.tag_value')])!!}
                            <span class="input-group-addon"><a href="javascript:" class="delete-item remove-icon"><span aria-hidden="true">×</span></a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="alert alert-info" style="font-size: 12px;">
        {!! trans('front.setflag_on_off_events_info') !!}
    </div>
    <div class="form-group">
        {!!Form::label('message', trans('validation.attributes.message').':')!!}
        {!!Form::text('message', null, ['class' => 'form-control'])!!}
    </div>
    {!!Form::close()!!}
@stop