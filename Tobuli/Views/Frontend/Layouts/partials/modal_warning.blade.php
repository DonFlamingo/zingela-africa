<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">{{ trans('global.title_'.$type) }}</h4>
        </div>
        <div class="modal-body">
            <div class="alert alert-{{ $type }}" role="alert">{{ $message }}</div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">{{ trans('global.close') }}</button>
        </div>
    </div>
</div>