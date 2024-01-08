<div class="tab-pane" id="routes_tab">
    <div class="tab-pane-header">
        <div class="form">
            <div class="input-group">
                <div class="form-group search">
                    {!!Form::text('search', null, ['class' => 'form-control', 'placeholder' => trans('front.search'), 'autocomplete' => 'off'])!!}
                </div>
                @if (Auth::User()->perm('routes', 'edit'))
                    <span class="input-group-btn">
                        <a href="javascript:" class="btn btn-primary" type="button" onClick="app.routes.create();">
                            <i class="icon add"></i>
                        </a>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="tab-pane-body">
        <div id="ajax-routes"></div>
    </div>
</div>

<div class="tab-pane" id="routes_create">
    {!! Form::hidden('polyline') !!}
    {!! Form::open(['route' => 'routes.store', 'method' => 'POST', 'id' => 'route_create']) !!}
    <div class="tab-pane-body">

        <div class="alert alert-info">
            {!!trans('front.please_draw_route')!!}
        </div>

        {!!Form::hidden('id')!!}

        <div class="form-group">
            {!!Form::label('name', trans('validation.attributes.name').':')!!}
            {!!Form::text('name', null, ['class' => 'form-control'])!!}
        </div>
        <div class="form-group">
            {!!Form::label('color', trans('validation.attributes.color').':')!!}
            {!!Form::text('color', '#1938FF', ['class' => 'form-control colorpicker'])!!}
        </div>

        <div class="buttons text-center">
            <a type="button" class="btn btn-action" href="javascript:" onClick="app.routes.store();">{!!trans('global.save')!!}</a>
            <a type="button" class="btn btn-default" href="javascript:" onClick="app.openTab('routes_tab');">{!!trans('global.cancel')!!}</a>
        </div>
    </div>
    {!!Form::close()!!}
</div>

<div class="tab-pane" id="routes_edit">
    {!! Form::hidden('polyline') !!}
    {!! Form::open(['route' => 'routes.update', 'method' => 'PUT', 'id' => 'route_update']) !!}
    <div class="tab-pane-body">

        <div class="alert alert-info">
            {!!trans('front.please_draw_route')!!}
        </div>
        {!!Form::hidden('id')!!}

        <div class="form-group">
            {!!Form::label('name', trans('validation.attributes.name').':')!!}
            {!!Form::text('name', null, ['class' => 'form-control'])!!}
        </div>
        <div class="form-group">
            {!!Form::label('color', trans('validation.attributes.color').':')!!}
            {!!Form::text('color', '#1938FF', ['class' => 'form-control colorpicker'])!!}
        </div>

        <div class="buttons text-center">
            <a type="button" class="btn btn-action" href="javascript:" onClick="app.routes.update();">{!!trans('global.save')!!}</a>
            <a type="button" class="btn btn-default" href="javascript:" onClick="app.openTab('routes_tab');">{!!trans('global.cancel')!!}</a>
        </div>
    </div>
    {!!Form::close()!!}
</div>