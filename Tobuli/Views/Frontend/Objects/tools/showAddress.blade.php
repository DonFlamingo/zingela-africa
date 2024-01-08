<div class="modal fade" id="showAddress">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span>×</span></button>
                <h4 class="modal-title"><i class="icon address"></i> {{ trans('front.show_address') }}</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger error" role="alert" style="display: none;"></div>

                {!! Form::open(['route' => 'objects.show_address', 'method' => 'POST']) !!}
                <div class="form-group">
                    {!! Form::label('address', trans('front.address').':') !!}
                    {!! Form::text('address', null, ['class' => 'form-control']) !!}
                </div>
                {!! Form::close() !!}
            </div>
            <div class="modal-footer">
                <div class="buttons">
                    @section('buttons')
                        <button type="button" class="btn btn-action" onclick="app.showAddress();">{!!trans('global.show')!!}</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
                    @show
                </div>
            </div>
        </div>
    </div>
</div>