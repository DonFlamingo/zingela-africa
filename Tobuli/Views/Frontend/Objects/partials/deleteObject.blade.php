<div class="modal fade" id="deleteObject">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <p class="modal-title">{{ trans('global.delete') }}</p>
            </div>
            <div class="modal-body">
                {!! trans('front.do_object_delete') !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-action" onclick="" data-dismiss="modal">{{ trans('global.yes') }}</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.no') }}</button>
            </div>
        </div>
    </div>
</div>