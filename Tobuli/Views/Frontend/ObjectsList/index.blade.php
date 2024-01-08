@extends('Frontend.Layouts.default')

@section('header-menu-items')
    @if ( Auth::User()->perm('devices', 'edit') )
        <li>
            <a href="javascript:" role="button" data-url="{!!route('objects.listview_settings.edit')!!}" data-modal="listview_settings_create">
                <span class="icon settings"></span>
                <span class="text">{!! trans('global.settings') !!}</span>
            </a>
        </li>
    @endif
@stop

@section('content')
    <div class="panel panel-default">
        <div class="panel-body">
            <div id="listview"></div>
        </div>
    </div>
@stop

@section('scripts')
    @include('Frontend.Layouts.partials.app')

    <script type="text/javascript">
        $(window).on("load", function() {
            app.listView.init();
            app.listView.list();
        });
    </script>
@stop
