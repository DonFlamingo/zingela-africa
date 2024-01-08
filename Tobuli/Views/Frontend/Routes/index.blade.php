@if (!empty($routes) && !empty($items = $routes->toArray()))
    <ul class="group-list">
        @foreach ($items as $key => $item)
            <?php $items[$key]['coordinates'] = json_decode($item['coordinates']); unset($items[$key]['polyline']);?>
            <li data-route-id="{{ $item['id'] }}">
                <div class="checkbox">
                    <input type="checkbox" name="route[{{ $item['id'] }}]" value="{{ $item['id'] }}" {{ !empty($item['active']) ? 'checked="checked"' : '' }} onChange="app.routes.active('{{ $item['id'] }}', this.checked);"/>
                    <label></label>
                </div>
                <div class="name">
                    <span data-mapicon="name">{{ $item['name'] }}</span>
                </div>
                <div class="details">
                    @if (Auth::User()->perm('routes', 'edit') || Auth::User()->perm('routes', 'remove'))
                        <div class="btn-group dropleft droparrow"  data-position="fixed">
                            <i class="btn icon options" data-toggle="dropdown" data-position="fixed" aria-haspopup="true" aria-expanded="false"></i>
                            <ul class="dropdown-menu" >
                                @if ( Auth::User()->perm('routes', 'edit') )
                                    <li>
                                        <a href='javascript:;' onclick="app.routes.edit({{ $item['id'] }});">
                                            <span class="icon edit"></span>
                                            <span class="text">{{ trans('global.edit') }}</span>
                                        </a>
                                    </li>
                                @endif
                                @if (Auth::User()->perm('routes', 'remove'))
                                    <li>
                                        <a href='#' data-target='#deleteRoute' onclick="app.routes.delete({{ $item['id'] }});" data-id='{{ $item['id'] }}' data-toggle='modal'>
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
    <script>app.routes.addMulti(jQuery.parseJSON('{!! json_encode($items) !!}'));</script>
@else
    <p class="no-results">{!! trans('front.no_routes') !!}</p>
@endif
