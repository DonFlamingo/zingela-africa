<div class="tab-pane-header">
    <div class="form">
        <div class="input-group">
            <div class="form-group search">
                {!!Form::text('search', null, ['class' => 'form-control', 'id' => 'alerts_search_field', 'placeholder' => trans('front.search'), 'autocomplete' => 'off'])!!}
            </div>
            <span class="input-group-btn">
                @if (Auth::User()->perm('alerts', 'edit'))
                    <button class="btn btn-primary" type="button" data-url="{{ route('alerts.create') }}" data-modal="alerts_create">
                        <i class="icon add"></i>
                    </button>
                @endif
            </span>
        </div>
    </div>
</div>

<div class="tab-pane-body" id="ajax-alerts"></div>