@if (!empty($alerts) && !empty($items = $alerts->toArray()))
    <ul class="group-list">
        @foreach ($items as $key => $item)
            <li data-alert-id="{{ $item['id'] }}">
                <div class="checkbox">
                    <input type="checkbox" name="alert[{{ $item['id'] }}]" value="{{ $item['id'] }}" {{ !empty($item['active']) ? 'checked="checked"' : '' }} onChange="app.alerts.active('{{ $item['id'] }}', this.checked);"/>
                    <label></label>
                </div>
                <div class="name">
                    <span data-alert="name">{{ $item['name'] }}</span>
                </div>
                <div class="details">
                    @if (Auth::User()->perm('alerts', 'edit') || Auth::User()->perm('alerts', 'remove'))
                        <div class="btn-group dropleft droparrow"  data-position="fixed">
                            <i class="btn icon options" data-toggle="dropdown" data-position="fixed" aria-haspopup="true" aria-expanded="false"></i>
                            <ul class="dropdown-menu" >
                                @if ( Auth::User()->perm('alerts', 'edit') )
                                    <li>
                                        <a href="javascript:;" data-url="{{ route('alerts.edit', $item['id']) }}" data-modal="alerts_edit">
                                            <span class="icon edit"></span>
                                            <span class="text">{{ trans('global.edit') }}</span>
                                        </a>
                                    </li>
                                @endif
                                @if (Auth::User()->perm('alerts', 'remove'))
                                    <li>
                                        <a href="javascript:;" data-url="{{ route('alerts.do_destroy', $item['id']) }}" data-modal="alerts_destroy">
                                            <span class="icon delete"></span>
                                            <span class="text">{{ trans('global.delete') }}</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>
@else
    <p class="no-results">{!! trans('front.no_alerts') !!}</p>
@endif
