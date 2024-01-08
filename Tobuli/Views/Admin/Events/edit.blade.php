@extends('Frontend.Layouts.modal')

@section('title', trans('global.edit'))

@section('body')
    {!!Form::open(['route' => 'admin.events.update', 'method' => 'PUT'])!!}
    {!!Form::hidden('id', $item->id)!!}
    <div class="form-group">
        <div class="checkbox">
            {!!Form::checkbox('always', 1, $item->always)!!}
            {!!Form::label(null, trans('admin.show_always'))!!}
        </div>
    </div>

    <div class="form-group">
        {!!Form::label('protocol', trans('validation.attributes.device_protocol').':')!!}
        {!!Form::select('protocol', $protocols,  $item->protocol, ['class' => 'form-control', 'data-live-search' => true])!!}
    </div>

    <div class="form-group">
        {!!Form::label('tag_value', trans('front.conditions').':')!!}
        <div class="empty-input-items">
            {!!Form::hidden('conditions')!!}

            @if (!empty($item->conditions))
                @foreach ($item->conditions as $condition)
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4 col-xs-4">
                                {!!Form::text('tag[]', $condition['tag'], ['class' => 'form-control', 'placeholder' => trans('validation.attributes.parameter')])!!}
                            </div>
                            <div class="col-md-4 col-xs-4">
                                {!!Form::select('type[]', $types, $condition['type'], ['class' => 'form-control'])!!}
                            </div>
                            <div class="col-md-4 col-xs-4">
                                <div class="input-group">
                                    {!!Form::text('tag_value[]', $condition['tag_value'], ['class' => 'form-control', 'placeholder' => trans('validation.attributes.tag_value')])!!}
                                    <span class="input-group-addon"><a href="javascript:" class="delete-item remove-icon"><span aria-hidden="true">×</span></a></span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
            <div class="form-group empty-input-add-new">
                <div class="row">
                    <div class="col-md-4 col-xs-4">
                        {!!Form::text('tag[]', null, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.tag')])!!}
                    </div>
                    <div class="col-md-4 col-xs-4">
                        {!!Form::select('type[]', ['1' => trans('front.event_type_1'), '2' => trans('front.event_type_2'), '3' => trans('front.event_type_3')], null, ['class' => 'form-control'])!!}
                    </div>
                    <div class="col-md-4 col-xs-4">
                        <div class="input-group">
                            {!!Form::text('tag_value[]', null, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.tag_value')])!!}
                            <span class="input-group-addon"><a href="javascript:" class="delete-item"><span aria-hidden="true">×</span></a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="alert alert-info" style="font-size: 12px;">
        {!!trans('front.setflag_on_off_events_info')!!}
    </div>
    <div class="form-group">
        {!!Form::label('message', trans('validation.attributes.message').':')!!}
        {!!Form::text('message', $item->message, ['class' => 'form-control'])!!}
    </div>
    {!!Form::close()!!}
@stop