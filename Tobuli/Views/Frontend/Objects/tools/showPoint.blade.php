<div class="modal fade" id="showPoint">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span>×</span></button>
                <h4 class="modal-title"><i class="icon point"></i> {{ trans('front.show_point') }}</h4>
            </div>

            <div class="modal-body">
            {!! Form::open() !!}
                <div class="form-group">
                    <div class="radio">
                        {!! Form::radio('cor_type', 1, true) !!}
                        {!! Form::label(null, 'Degrees, minutes, and seconds') !!}
                    </div>
                </div>

                <div class="form-group">
                    <div class="radio">
                        {!! Form::radio('cor_type', 2) !!}
                        {!! Form::label(null, 'Decimal degrees') !!}
                    </div>
                </div>

                <div class="cor_type_1 row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!!Form::label('latitude', trans('global.latitude').':')!!}
                            {!!Form::text('latitude', null, ['class' => 'form-control', 'placeholder' => 'Ex: 38:42:4.98'])!!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!!Form::label('longitude', trans('global.longitude').':')!!}
                            {!!Form::text('longitude', null, ['class' => 'form-control', 'placeholder' => 'Ex: -9:9:48.30'])!!}
                        </div>
                    </div>
                </div>
                <div class="cor_type_2 row" style="display: none;">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!!Form::label('latitude', trans('global.latitude').':')!!}
                            {!!Form::text('latitude', null, ['class' => 'form-control', 'placeholder' => 'Ex: 38.701383'])!!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!!Form::label('longitude', trans('global.longitude').':')!!}
                            {!!Form::text('longitude', null, ['class' => 'form-control', 'placeholder' => 'Ex: -9.163417'])!!}
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-action" data-dismiss="modal">{!!trans('global.show')!!}</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.close')!!}</button>
            </div>
        </div>
    </div>
</div>
<script>

</script>