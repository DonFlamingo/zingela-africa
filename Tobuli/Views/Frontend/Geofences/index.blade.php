@if (!empty($grouped))
    @foreach ($grouped as $id => $items)
        <div class="group" data-toggle="multiCheckbox">
            <div class="group-heading">

                <div class="checkbox">
                    <input type="checkbox" data-toggle="checkbox">
                    <label></label>
                </div>

                <div class="group-title {{ isset($geofence_groups_opened[$id]) ? '' : 'collapsed' }}" data-toggle="collapse" data-target="#geofence-group-{{ $id }}" data-parent="#geofences_tab" aria-expanded="false" aria-controls="geofence-group-{{ $id }}">
                    {{ $geofence_groups[$id] }} <span class="count">{{ count($items) }}</span>
                </div>
            </div>

            <div id="geofence-group-{{ $id }}" class="group-collapse collapse {{ !isset($geofence_groups_opened[$id]) ? '' : 'in' }}" data-id="{{ $id }}" role="tabpanel">
                <div class="group-body">
                    <ul class="group-list">
                        @foreach ($items as $key => $item)
                            <?php $items[$key]['coordinates'] = json_decode($item['coordinates']); unset($items[$key]['polygon']);?>
                            <li data-geofence-id="{{ $item['id'] }}">
                                <div class="checkbox">
                                    <input type="checkbox" name="items[{{ $item['id'] }}]" value="{{ $item['id'] }}" {{ !empty($item['active']) ? 'checked="checked"' : '' }} onChange="app.geofences.active('{{ $item['id'] }}', this.checked);"/>
                                    <label></label>
                                </div>
                                <div class="name">
                                    <span data-geofence="name">{{ $item['name'] }}</span>
                                </div>
                                <div class="details">
                                    @if (Auth::User()->perm('geofences', 'edit') || Auth::User()->perm('geofences', 'remove'))
                                        <div class="btn-group dropleft droparrow"  data-position="fixed">
                                            <i class="btn icon options" data-toggle="dropdown" data-position="fixed" aria-haspopup="true" aria-expanded="false"></i>
                                            <ul class="dropdown-menu" >
                                                @if ( Auth::User()->perm('geofences', 'edit') )
                                                    <li>
                                                        <a href='javascript:;' onclick="app.geofences.edit({{ $item['id'] }});">
                                                            <span class="icon edit"></span>
                                                            <span class="text">{{ trans('global.edit') }}</span>
                                                        </a>
                                                    </li>
                                                @endif
                                                @if (Auth::User()->perm('geofences', 'remove'))
                                                    <li>
                                                        <a href='#' data-target='#deleteGeofence' data-toggle='modal' onclick="app.geofences.delete({{ $item['id'] }});">
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
                    <script>app.geofences.addMulti({!! json_encode($items) !!});</script>
                </div>
            </div>
        </div>
    @endforeach
@else
    <p class="no-results">{!! trans('front.no_geofences') !!}</p>
@endif
