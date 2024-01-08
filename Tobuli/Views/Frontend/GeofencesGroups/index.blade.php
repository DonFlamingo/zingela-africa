@extends('Frontend.Layouts.modal')

@section('title')
    {{ trans('front.groups') }}
@stop

@section('body')
    {!! Form::open(['route' => 'geofences_groups.store', 'method' => 'POST']) !!}
    {!! Form::hidden('id') !!}
    <div class="empty-input-items">
        @foreach ($groups as $group)
            <div class="form-group">
                <div class="input-group">
                    {!! Form::text("edit_group[{$group->id}]", $group->title, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.name')]) !!}
                    <span class="input-group-addon"><a href="javascript:" class="delete-item"><span aria-hidden="true">×</span></a></span>
                </div>
            </div>
        @endforeach
        <div class="form-group empty-input-add-new" @if (count($groups) >= 41)style="display: none;"@endif>
            <div class="input-group">
                {!! Form::text('add_group[]', null, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.name')]) !!}
                <span class="input-group-addon"><a href="javascript:" class="delete-item"><span aria-hidden="true">×</span></a></span>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@stop