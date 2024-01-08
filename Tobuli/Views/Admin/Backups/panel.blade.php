<div class="panel panel-default">

    <div class="panel-heading">
        <div class="panel-title">{{ trans('front.database_backups') }}</div>
    </div>

    <div class="panel-body">
        {!! Form::open(['route' => 'admin.backups.save', 'method' => 'POST', 'class' => 'form form-horizontal', 'id' => 'database-backup-form']) !!}

        <div class="form-group">
            {!! Form::label('ftp_server', trans('validation.attributes.ftp_server'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text('ftp_server', isset($settings['ftp_server']) ? $settings['ftp_server'] : null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('ftp_port', trans('validation.attributes.ftp_port'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text('ftp_port', isset($settings['ftp_port']) ? $settings['ftp_port'] : 21, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('ftp_username', trans('validation.attributes.ftp_username'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text('ftp_username', isset($settings['ftp_username']) ? $settings['ftp_username'] : null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('ftp_password', trans('validation.attributes.ftp_password'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::password('ftp_password', ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('ftp_path', trans('validation.attributes.ftp_path'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text('ftp_path', isset($settings['ftp_path']) ? $settings['ftp_path'] : '/', ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('period', trans('validation.attributes.period'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::select('period', $periods, isset($settings['period']) ? $settings['period'] : null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('hour', trans('validation.attributes.hour'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::select('hour', $hours, isset($settings['hour']) ? $settings['hour'] : null, ['class' => 'form-control']) !!}
                <small>{{ trans('front.server_time_now') }} {{ date('Y-m-d H:i:s') }} (UTC 00:00)</small>
            </div>
        </div>

        @if (isset($settings['ftp_server']))
            <hr>
            <h4>
                {{ trans('front.test_ftp_upload') }}
            </h4>
            <button class="btn btn-main test_ftp" type="button" onClick="test_ftp();"><i class="icon upload"></i> {{ trans('front.test_ftp') }}</button>
            <div class="test_ftp_response alert alert-danger" style="display: none;">
            </div>
        @endif

        {!! Form::close() !!}
    </div>

    <div class="panel-footer">
        <button type="submit" class="btn btn-action" onClick="$('#database-backup-form').submit();">{{ trans('global.save') }}</button>
        <button type="button" class="btn btn-default" data-modal="latest_uploads" data-url="{{ route('admin.backups.logs') }}">{{ trans('front.latest_uploads') }}</button>
    </div>
</div>

<script>
    function test_ftp() {
        $.ajax({
            type: 'GET',
            url: '{{ route('admin.backups.test') }}',
            beforeSend: function() {
                $('.test_ftp_response').hide();
            },
            success: function(res) {
                if (res.status == 1)
                    $('.test_ftp_response').show().removeClass('alert-danger').addClass('alert-success').html(res.message);
                else
                    $('.test_ftp_response').show().removeClass('alert-success').addClass('alert-danger').html(res.message);
            }
        });
    }
</script>