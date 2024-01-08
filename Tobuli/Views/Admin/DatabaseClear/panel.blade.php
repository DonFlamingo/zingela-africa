<div class="panel panel-default">

    <div class="panel-heading">
        <div class="panel-title">{{ trans('admin.database_clear') }}</div>
    </div>

    <div class="panel-body">
        {!! Form::open(array('route' => 'admin.db_clear.save', 'method' => 'POST', 'class' => 'form form-horizontal', 'id' => 'database-clear-form')) !!}

        <div class="form-group">
            <div class="col-xs-12">
                <div class="checkbox">
                    {!! Form::checkbox('status', 1, !empty($settings['status'])) !!}
                    {!! Form::label('status', trans('validation.attributes.database_clear_status') ) !!}
                </div>
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('days', trans('validation.attributes.database_clear_days'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text('days', isset($settings['days']) ? $settings['days'] : 90, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label(null, trans('front.database_size'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text(null, $size, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
            </div>
        </div>

        {!! Form::close() !!}
    </div>

    <div class="panel-footer">
        <button type="submit" class="btn btn-action" onClick="$('#database-clear-form').submit();">{{ trans('global.save') }}</button>
    </div>
</div>