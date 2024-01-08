<div class="widget widget-sensors">
    <div class="widget-heading">
        <div class="widget-title"><i class="icon sensors"></i> {{ trans('front.sensors') }}</div>
    </div>
    <div class="widget-body">
        <table class="table" data-device="sensors"></table>

        <div class="widget-empty">
            <a href="javascript:" class="btn btn-default" data-url="" data-modal="sonsors_create" type="button">
                <i class="icon add"></i> {{ trans('front.add_sensor') }}
            </a>
        </div>
    </div>
</div>