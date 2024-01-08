<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span>×</span></button>
            <h4 class="modal-title">Delete Plan</h4>
        </div>
        <div class="modal-body">
            Are you sure you want to delete billing plan?
        </div>
        <div class="modal-footer">
            <div class="buttons">
                @section('footer')
                    <button id="deleteBillingPlan" type="button" class="btn btn-action" data-dismiss="modal">{!!trans('global.yes')!!}</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.no')!!}</button>
                @show
            </div>
        </div>
    </div>
</div>
<script>
	$( function() {
  		$( "#deleteBillingPlan" ).click(function(e){
        	e.preventDefault();
        	$.post("{{route('admin.billing.billing_plans_destroyOne', ['id' => $id])}}", function(data, status){
        		location.reload();
    		});
        });
	});
</script>