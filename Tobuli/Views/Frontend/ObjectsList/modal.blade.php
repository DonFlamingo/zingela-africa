@extends('Frontend.Layouts.modal')

@section('modal_class')
modal-lg
@stop

@section('title')
    {!!trans('front.object_listview')!!}
@stop

@section('body')
    @if ( Auth::User()->perm('devices', 'edit') )
        <div class="action-block">
            <a href="javascript:" class="btn btn-action" data-url="{!!route('objects.listview_settings.edit')!!}" data-modal="listview_settings_create" type="button">
                <i class="icon settings"></i> {{ trans('front.settings') }}
            </a>
        </div>
    @endif

    <div id="listview"></div>

    <script>
        app.listView.list();
    </script>
@stop

@section('buttons')
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.close')!!}</button>
@stop