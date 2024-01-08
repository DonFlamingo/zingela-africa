<div class="tab-pane-header">
    <div class="form">
        <div class="input-group">
            <div class="form-group search">
                {!!Form::text('search', null, ['class' => 'form-control', 'id' => 'events_search_field', 'placeholder' => trans('front.search'), 'autocomplete' => 'off'])!!}
            </div>
            <span class="input-group-btn">
                <button class="btn btn-default" type="button" data-url="{!!route('events.do_destroy')!!}" data-modal="events_do_destroy">
                    <i class="icon remove-all"></i>
                </button>
            </span>
        </div>
    </div>
</div>

<div class="tab-pane-body">
    <table class="table table-list">
        <thead>
            <tr>
                <th>{{ trans('front.time') }}</th>
                <th>{{ trans('front.object') }}</th>
                <th>{{ trans('front.event') }}</th>
            </tr>
        </thead>

        <tbody id="ajax-events"></tbody>
    </table>
</div>