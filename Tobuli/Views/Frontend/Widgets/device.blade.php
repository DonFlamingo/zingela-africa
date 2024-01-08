<div class="widget widget-device">
    <div class="widget-heading">
        <div class="widget-title">
            <div class="pull-right">
                <span data-device="status"></span> <span data-device="status-text"></span>
            </div>
            <i class="icon device"></i>
            <span data-device="name"></span>
        </div>
    </div>
    <div class="widget-body">
        <table class="table">
            <tbody>
            <tr>
                <td>{{ trans('front.address') }}:</td>
                <td><span data-device="address"></span></td>
            </tr>
            <tr>
                <td>{{ trans('front.time') }}:</td>
                <td><span data-device="time"></span></td>
            </tr>
            <tr>
                <td>{{ trans('front.stop_duration') }}:</td>
                <td><span data-device="stoptime" data-id=""></span></td>
            </tr>
            <tr>
                <td>{{ trans('front.driver') }}:</td>
                <td><span data-device="driver"></span></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>