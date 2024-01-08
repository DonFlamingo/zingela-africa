<div class="tab-pane-header">
    <div id="history-form" class="form-horizontal">
        <div class="form-group">
            <label class="col-xs-3 control-label">{!!trans('global.device')!!}:</label>
            <div class="col-xs-9">
                {!!Form::select('devices', $devices, $history['def_device'], ['class' => 'form-control devices_list', 'data-live-search' => true])!!}
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-3 control-label">{!!trans('global.from')!!}:</label>
            <div class="col-xs-9">
                <div class="input-group">
                    {!!Form::text('from_date', $history['start'], ['class' => 'datepicker form-control'])!!}
                    <span class="input-group-btn">
                        {!!Form::select('from_time', Config::get('tobuli.history_time'), null, ['class' => 'form-control timeselect'])!!}
                    </span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-3 control-label">{!!trans('global.to')!!}:</label>
            <div class="col-xs-9">
                <div class="input-group">
                    {!!Form::text('to_date', $history['end'], ['class' => 'datepicker form-control'])!!}
                    <span class="input-group-btn">
                        {!!Form::select('to_time', Config::get('tobuli.history_time'), $history['end_time'], ['class' => 'form-control timeselect'])!!}
                    </span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-3 control-label"></label>
            <div class="col-xs-9">
                <div class="checkbox">
                    {!! Form::checkbox('snap_to_road', 1, 0, ['id' => 'snap_to_road']) !!}
                    <label>{!!trans('front.snap_to_road')!!}</label>
                </div>
            </div>
        </div>

        <div class="input-group">
            <button class="btn btn-primary btn-block" type="button" onclick="app.history.get()">{!!trans('front.show_history')!!}</button>
            <span class="input-group-btn">
                <div class="btn-group dropdown">
                    <button class="btn btn-default" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="icon history-export"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:" onclick="app.history.export( 'gsr' )">{{ trans('front.export_gsr') }}</a></li>
                        <li><a href="javascript:" onclick="app.history.export( 'kml' )">{{ trans('front.export_kml') }}</a></li>
                        <li><a href="javascript:" onclick="app.history.export( 'gpx' )">{{ trans('front.export_gpx') }}</a></li>
                        <li><a href="javascript:" onclick="app.history.export( 'csv' )">{{ trans('front.export_csv') }}</a></li>
                    </ul>
                </div>
                <button class="btn btn-default" type="button" onclick="app.history.clear()">
                    <i class="icon history-clean"></i>
                </button>
            </span>
        </div>
    </div>
</div>
<div class="tab-pane-body">
	<div id="history-geofence"  style="padding: 10px;">
		<div class="input-group" id="history_geofence_button">
			<a href="javascript:" class="btn btn-primary btn-block" type="button" onclick="app.geofences.create_history();">
				<i class="icon add"></i> Convert History to geofence
			</a>
		</div>
		
		<div class="" id="history_geofencing_create" style="display:none;">
			<div class="tab-pane-header">
				<!--<div class="alert alert-info">
					{!!trans('front.please_draw_polygon')!!}
				</div>-->	
			</div>

			{!! Form::hidden('polygon') !!}
			{!! Form::open(['route' => 'geofences.store', 'method' => 'POST', 'class' => 'form', 'id' => 'geofence_create']) !!}
			<div class="tab-pane-body2">
				<div class="form-group">
					{!! Form::label('name', trans('validation.attributes.name').':') !!}
					{!! Form::text('name', null, ['class' => 'form-control']) !!}
				</div>
				<div class="form-group" style="display:none;">
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
					<a type="button" class="btn btn-action" href="javascript:" onClick="app.geofences.store_history();">{!!trans('global.save')!!}</a>
					<a type="button" class="btn btn-default" href="javascript:" onClick="app.geofences.close_history();">{!!trans('global.cancel')!!}</a>
				</div>
			</div>
			{!!  Form::close() !!}
		</div>	
	</div>
    <div id="ajax-history"></div>
</div>