<div class="tab-pane-header">
    <div class="form">
        <div class="input-group">
            <div class="form-group search">
                {!!Form::text('search', null, ['class' => 'form-control', 'placeholder' => trans('front.search'), 'autocomplete' => 'off'])!!}
            </div>
            <span class="input-group-btn">
                {{--
                <button class="btn btn-default" type="button">
                    <i class="icon filter"></i>
                </button>
                --}}
                @if ( settings('plugins.object_listview.status') && Auth::User()->perm('devices', 'view') )
                    <a href="{{ route('objects.listview') }}" class="btn btn-primary" target="_blank">
                        <i class="icon list"></i>
                    </a>
                @endif
                @if (Auth::User()->perm('devices', 'edit'))
                <button class="btn btn-primary" type="button"  data-url="{!!route('devices.create')!!}" data-modal="devices_create">
                    <i class="icon add"></i>
                </button>
                @endif
            </span>
        </div>
    </div>
</div>

<div class="tab-pane-body">
    <div id="ajax-items"></div>
</div>