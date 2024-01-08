<div class="tab-pane" id="geofencing_tab">
    <div class="tab-pane-header">
        <div class="form">
            <div class="input-group">
                <div class="form-group search">
                    {!!Form::text('search', null, ['class' => 'form-control', 'placeholder' => trans('front.search'), 'autocomplete' => 'off'])!!}
                </div>
                @if (Auth::User()->perm('geofences', 'edit'))
                <span class="input-group-btn">
                    <div class="btn-group dropdown">
                        <button class="btn btn-default" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon edit"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="javascript:" data-url="{{ route('geofences.export') }}" data-modal="geofences_export">{{ trans('front.export') }}</a></li>
                            <li><a href="javascript:" onclick="app.geofences.import();">{{ trans('front.import') }}</a></li>
                        </ul>
                    </div>

                    <a href="javascript:" class="btn btn-primary" type="button" onClick="app.geofences.create();">
                        <i class="icon add"></i>
                    </a>
                </span>
                @endif
            </div>
        </div>
    </div>

    <div class="tab-pane-body">
        <div id="ajax-geofences"></div>
    </div>
</div>

<div class="tab-pane" id="geofencing_create">
    <div class="tab-pane-header">
        <div class="alert alert-info">
            {!!trans('front.please_draw_polygon')!!}
        </div>
    </div>

    {!! Form::hidden('polygon') !!}
    {!! Form::open(['route' => 'geofences.store', 'method' => 'POST', 'class' => 'form', 'id' => 'geofence_create']) !!}
    <div class="tab-pane-body">
        <div class="form-group">
            {!! Form::label('name', trans('validation.attributes.name').':') !!}
            {!! Form::text('name', null, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('group_id', trans('validation.attributes.group_id').':') !!}
            <div class="input-group">
                <div class="geofence_groups_select_ajax">

                </div>
                {!! Form::select('group_id', $geofence_groups, null, ['class' => 'form-control geofence_groups_select']) !!}
                <span class="input-group-btn">
                    <a href="javascript:" class="btn btn-primary" data-url="{{ route('geofences_groups.index') }}" data-modal="geofence_groups" title="{{ trans('front.add_group') }}">
                        <i class="icon add"></i>
                    </a>
                </span>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('polygon_color', trans('validation.attributes.polygon_color').':') !!}
            {!! Form::text('polygon_color', '#D000DF', ['class' => 'form-control colorpicker']) !!}
        </div>

        <div class="buttons text-center">
            <a type="button" class="btn btn-action" href="javascript:" onClick="app.geofences.store();">{!!trans('global.save')!!}</a>
            <a type="button" class="btn btn-default" href="javascript:" onClick="app.openTab('geofencing_tab');">{!!trans('global.cancel')!!}</a>
        </div>
    </div>
    {!!  Form::close() !!}
</div>

<div class="tab-pane" id="geofencing_edit">
    {!! Form::hidden('polygon') !!}
    {!! Form::open(['route' => 'geofences.update', 'method' => 'PUT', 'id' => 'geofence_update']) !!}
    <div class="tab-pane-body">

        <div class="form-group">
            {!! Form::label('name', trans('validation.attributes.name').':') !!}
            {!! Form::text('name', null, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('group_id', trans('validation.attributes.group_id').':') !!}
            <div class="input-group">
                <div class="geofence_groups_select_ajax">

                </div>
                {!! Form::select('group_id', $geofence_groups, null, ['class' => 'form-control geofence_groups_select']) !!}
                <span class="input-group-btn">
                    <a href="javascript:" class="btn btn-primary" data-url="{{ route('geofences_groups.index') }}" data-modal="geofence_groups" title="{{ trans('front.add_group') }}">
                        <i class="icon add"></i>
                    </a>
                </span>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('polygon_color', trans('validation.attributes.polygon_color').':') !!}
            {!! Form::text('polygon_color', '#D000DF', ['class' => 'form-control colorpicker']) !!}
        </div>

        <div class="buttons text-center">
            <a type="button" class="btn btn-action" href="javascript:" onClick="app.geofences.update();">{!!trans('global.save')!!}</a>
            <a type="button" class="btn btn-default" href="javascript:" onClick="app.openTab('geofencing_tab');">{!!trans('global.cancel')!!}</a>
        </div>
    </div>
    {!!  Form::close() !!}
</div>