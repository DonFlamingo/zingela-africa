<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title thin" id="modalErrorLabel">{{ trans('global.error_occurred') }}</h4>
        </div>
        <div class="modal-body">
            <p class="alert alert-danger">{{ $error }}</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-side default" data-dismiss="modal" aria-hidden="true">{{ trans('global.close') }}</button>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->