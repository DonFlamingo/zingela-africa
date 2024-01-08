<div class="modal fade" id="getObejctsFailed">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                Failed to recover devices.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">{{ trans('global.close') }}</button>
                <button type="button" class="btn btn-action" onclick="app.devices.list();" data-dismiss="modal">{{ trans('global.try_again') }}</button>
            </div>
        </div>
    </div>
</div>