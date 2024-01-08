@if (!empty($grouped))
    @foreach ($grouped as $id => $devices)
        <div class="group" data-toggle="multiCheckbox">
            <div class="group-heading">

                <div class="checkbox">
                    <input type="checkbox" data-toggle="checkbox">
                    <label></label>
                </div>

                <div class="group-title {{ isset($device_groups_opened[$id]) ? '' : 'collapsed' }}" data-toggle="collapse" data-target="#device-group-{{ $id }}" data-parent="#objects_tab" aria-expanded="false" aria-controls="device-group-{{ $id }}">
                    {{ $device_groups[$id] }} <span class="count">{{ count($devices) }}</span>
                </div>
                {{--
                @if ($id)
                    <div class="btn-group dropleft droparrow"  data-position="fixed">
                        <i class="btn icon options" data-toggle="dropdown" data-position="fixed" aria-haspopup="true" aria-expanded="false"></i>
                    </div>
                @endif
                --}}
            </div>

            <div id="device-group-{{ $id }}" class="group-collapse collapse {{ !isset($device_groups_opened[$id]) ? '' : 'in' }}" data-id="{{ $id }}" role="tabpanel">
                <div class="group-body">
                    <ul class="group-list">
                        @foreach ($devices as $key => $item)
                            <li data-device-id="{{ $item['id'] }}">
                                <div class="checkbox">
                                    <input type="checkbox" name="items[{{ $item['id'] }}]" value="{{ $item['id'] }}" {{ !empty($item['pivot']['active']) ? 'checked="checked"' : '' }} onChange="app.devices.active('{{ $item['id'] }}', this.checked);"/>
                                    <label></label>
                                </div>
                                <div class="name" onClick="app.devices.select({{ $item['id'] }});">
                                    <span data-device="name">{{ $item['name'] }}</span>
                                </div>
                                <div class="details">
                                    <span data-device="speed"></span>
                                    @if ( $item['engine_hours'] != 'gps' )
                                    <span data-device="detect_engine"><i class="icon detect_engine"></i></span>
                                    @endif
                                    <span data-device="status" data-toggle="tooltip" data-placement="top" title=""></span>

                                    <div class="btn-group dropleft droparrow"  data-position="fixed">
                                        <i class="btn icon options" data-toggle="dropdown" data-position="fixed" aria-haspopup="true" aria-expanded="false"></i>
                                        <ul class="dropdown-menu" >
                                            @if ( Auth::User()->perm('history', 'view') )
                                                <li>
                                                    <a href="javascript:" class="object_show_history" onClick="app.history.device('{{ $item['id'] }}', 'last_hour');">
                                                        <span class="icon last-hour"></span>
                                                        <span class="text">{{ trans('front.show_history') }} ({{ mb_strtolower(trans('front.last_hour')) }})</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:" class="object_show_history" onClick="app.history.device('{{ $item['id'] }}', 'today');">
                                                        <span class="icon today"></span>
                                                        <span class="text">{{ trans('front.show_history') }} ({{ mb_strtolower(trans('front.today')) }})</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:" class="object_show_history" onClick="app.history.device('{{ $item['id'] }}', 'yesterday');">
                                                        <span class="icon yesterday"></span>
                                                        <span class="text">{{ trans('front.show_history') }} ({{ mb_strtolower(trans('front.yesterday')) }})</span>
                                                    </a>
                                                </li>
                                            @endif

                                            <li>
                                                <a href="javascript:" data-url="{{ route('devices.follow_map', [$item['id']]) }}" data-id="{{ $item['id'] }}" onClick="app.devices.follow({{ $item['id'] }});" data-name="{{ trans('front.follow').' ('.$item['name'].')' }}">
                                                    <span class="icon follow"></span>
                                                    <span class="text">{{ trans('front.follow') }}</span>
                                                </a>
                                            </li>

                                            @if ( Auth::User()->perm('send_command', 'view') )
                                                <li>
                                                    <a href="javascript:" data-url="{{ route('send_command.create') }}" data-modal="send_command" data-id="{{ $item['id'] }}">
                                                        <span class="icon send-command"></span>
                                                        <span class="text">{{ trans('front.send_command') }}</span>
                                                    </a>
                                                </li>
                                            @endif

                                            @if ( Auth::User()->perm('devices', 'edit') )
                                                <li>
                                                    <a href="javascript:" data-url="{{ route('devices.edit', [$item['id'], 0]) }}" data-modal="devices_edit">
                                                        <span class="icon edit"></span>
                                                        <span class="text">{{ trans('global.edit') }}</span>
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </li>
                            <?php
                            unset($devices[$key]['traccar'], $devices[$key]['traccar_device_id'], $devices[$key]['parameters']);
                            ?>
                        @endforeach
                    </ul>
                    <script>app.devices.addMulti(JSON.parse('{!! json_encode($devices) !!}'));</script>
                </div>
            </div>
        </div>
    @endforeach
@else
    <p class="no-results">{!! trans('front.no_devices') !!}</p>
@endif
