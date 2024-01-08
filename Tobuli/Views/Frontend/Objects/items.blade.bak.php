@if (!empty($grouped))
    @foreach ($grouped as $id => $devices)
        <div class="panel-heading group_toggle_devices" role="tab" id="heading{{ $id }}" data-id="{{ $id }}">
                <span class="panel-title">
                    <div class="checkbox checkbox-primary">
                        {!! Form::checkbox('select_all') !!}
                        <label>
                            <a role="button" class="{{ isset($device_groups_opened[$id]) ? '' : 'collapsed' }}" data-toggle="collapse" href="#collapse{{ $id }}" aria-expanded="true" aria-controls="collapse{{ $id }}">{{ $device_groups[$id] }} <span class="framed">{{ count($devices) }}</span> <span class="status-icon"></span></a>
                        </label>
                    </div>
                </span>
        </div>
        <div id="collapse{{ $id }}" class="panel-collapse collapse {{ !isset($device_groups_opened[$id]) ? '' : 'in' }}" data-id="{{ $id }}" role="tabpanel" aria-labelledby="heading{{ $id }}">
            <div class="panel-body">
                <div class="object-container">
                    <ul>
                        @foreach ($devices as $item)
                            <?php
                            $values = [
                                    'odometer' => [
                                            'value' => 0,
                                            'sufix' => ''
                                    ],
                                    'engine_hours' => [
                                            'value' => 0,
                                            'sufix' => ''
                                    ]
                            ];
                            $item['search'] = utf8_strtolower($item['name']);
                            if (strlen($item['name']) > 18)
                                $item['name'] = utf8_substr($item['name'], 0, 18).'...';
                            $alarm = null;
                            $protocol = null;
                            if (isset($item['traccar']['other'])) {
                                //preg_match( '/<alarm>(.*?)<\/alarm>/s', $item['traccar']['other'], $alarm );
                                preg_match( '/<protocol>(.*?)<\/protocol>/s', $item['traccar']['other'], $protocol );
                            }
                            if ($item['expiration_date'] != '0000-00-00' && strtotime($item['expiration_date']) < strtotime(date('Y-m-d'))) {
                                $item['traccar'] = [
                                    'time' => trans('front.expired'),
                                    'server_time' => '',
                                    'other' => ''
                                ];

                                $time = trans('front.expired');
                            }
                            else {
                                if (empty($item['traccar']['time']) || substr($item['traccar']['time'], 0, 4) == '0000') {
                                   $time = trans('front.not_connected');
                                }
                                else {
                                   $time = datetime($item['traccar']['time'], TRUE, isset($timezones[$item['pivot']['timezone_id']]) ? $timezones[$item['pivot']['timezone_id']] : NULL);
                                }
                            }

                            //$dev_online = isDeviceOnline($item['traccar']['server_time'], isset($item['traccar']['ack_time']) ? $item['traccar']['ack_time'] : NULL);

                            $dev_online = getDeviceStatus($item);
                            $icon_color = getDeviceStatusColor($item, $dev_online);

                            $icon_colors = [];
                            if ($item['icon']['type'] == 'arrow') {
                                $icon_colors = $item['icon_colors'];
                            }

                            $demos = Config::get('tobuli.demos');
                            $speed = '0';
                            $altitude = '0';
                            if (isset($item['traccar']['speed']) && $dev_online == 'online')
                                $speed = Auth::User()->unit_of_distance == 'mi' ? kilometersToMiles($item['traccar']['speed']) : $item['traccar']['speed'];
                            if (isset($item['traccar']['altitude']))
                                $altitude = Auth::User()->unit_of_altitude == 'ft' ? metersToFeets($item['traccar']['altitude']) : $item['traccar']['altitude'];

                            $driver_id = null;
                            if (!empty($item->pivot->current_driver_id))
                                $driver_id = $item->pivot->current_driver_id;

                            if (!$dev_online)
                                $item['tail_length'] = 0;

                            $tail = prepareDeviceTail(isset($item['traccar']['latest_positions']) ? $item['traccar']['latest_positions'] : '', $item['tail_length']);

                            ?>
                            <li class="li_pointer item-{{ $item['id'] }} {{ $dev_online }}">
                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox" name="items[{!! $item['id'] !!}]" value="{!! $item['id'] !!}"
                                           data-lat="{!! cord(isset($item['traccar']['lastValidLatitude']) ? $item['traccar']['lastValidLatitude'] : 0) !!}"
                                           data-lng="{!! cord(isset($item['traccar']['lastValidLongitude']) ? $item['traccar']['lastValidLongitude'] : 0) !!}"
                                           data-speed="{!! round($speed) !!}"
                                           data-altitude="{!! round($altitude) !!}"
                                           data-power="{!! isset($item['traccar']['power']) ? $item['traccar']['power'] : '-' !!}"
                                           data-course="{!! isset($item['traccar']['course']) ? $item['traccar']['course'] : '-' !!}"
                                           data-icon="{!! $item['icon']['path'] !!}"
                                           data-icon-type="{!! $item['icon']['type'] !!}"
                                           data-icon-width="{!! $item['icon']['width'] !!}"
                                           data-icon-height="{!! $item['icon']['height'] !!}"
                                           data-iconcolor="{!! !is_null($icon_color) ? $icon_color : 'green' !!}"
                                           data-name="{!! $item['name'] !!}"
                                           data-search="{!! $item['search'] !!}"
                                           data-tail-color="{!! $item['tail_color'] !!}"
                                           data-time="{!! $time !!}"
                                           data-server-time="{{ (isset($item['traccar']['server_time']) ? $item['traccar']['server_time'] : '-') }}"
                                           data-timestamp="{!! (isset($item['traccar']['server_time']) ? strtotime($item['traccar']['server_time']) : 0) !!}"
                                           data-acktimestamp="{!! (isset($item['traccar']['ack_time']) ? strtotime($item['traccar']['ack_time']) : 0) !!}"
                                           data-address=""
                                           data-id="{!! $item['id'] !!}"
                                           data-online="{!! $dev_online !!}"
                                           {{--data-alarm="{!! isset($alarm['0']) ? $alarm['1'] : '-' !!}"--}}
                                           data-protocol="{!! isset($item['traccar']['protocol']) && Auth::User()->perm('protocol', 'view') ? $item['traccar']['protocol'] : '-' !!}"
                                           data-driver="{!! isset($drivers[$driver_id]) ? $drivers[$driver_id] : '-' !!}"
                                           data-device-model="{{ (!empty($item['device_model']) ? $item['device_model'] : '-') }}"
                                           data-plate-number="{{ (!empty($item['plate_number']) ? $item['plate_number'] : '-') }}"
                                    <?php echo $item['pivot']['active'] ? 'checked="checked"' : ''; ?>>
                                    <label></label>
                                    <span class="sensors-{{ $item['id'] }}" style="display: none;">{!! json_encode(formatSensors($item['traccar']['other'], $item['sensors'], $values)) !!}</span>
                                    <span class="services-{{ $item['id'] }}" style="display: none;">{!! json_encode(formatServices($item['services'], $values)) !!}</span>
                                    <span class="tail-{{ $item['id'] }}" style="display: none;">{!! json_encode($tail) !!}</span>
                                    <span class="icon-colors-{{ $item['id'] }}" style="display: none;">{!! json_encode($icon_colors) !!}</span>
                                </div>
                                <div class="details">
                                    <span class="object-status {{ $icon_color }}"></span>{{ $item['name'] }}
                                    <i class="device-time">{{ $time }}</i>
                                </div>
                                <div class="speed">
                                    <span class="framed"><span class="device-speed">{{ round($speed) }}</span> {{ Auth::User()->distance_unit_hour }}</span>
                                </div>

                                <?php
                                $popup = '';
                                if ( Auth::User()->perm('history', 'view') ) {
                                    $popup .= "<div><a href='javascript:;' class='object_show_history' data-id='{$item['id']}' data-period='last_hour'>".trans('front.show_history')." (".mb_strtolower(trans('front.last_hour')).")</a></div>
                                               <div><a href='javascript:;' class='object_show_history' data-id='{$item['id']}' data-period='today'>".trans('front.show_history')." (".mb_strtolower(trans('front.today')).")</a></div>
                                               <div><a href='javascript:;' class='object_show_history' data-id='{$item['id']}' data-period='yesterday'>".trans('front.show_history')." (".mb_strtolower(trans('front.yesterday')).")</a></div>";
                                }

                                $popup .= "<div><a href='javascript:;' class='follow-object' data-url='". route('devices.follow_map', [$item['id']])."' data-id='{$item['id']}' data-name='". trans('front.follow')." (".$item['name'].")'>". trans('front.follow') ."</a></div>";

                                if ( Auth::User()->perm('send_command', 'view') ) {
                                    $popup .= "<div><a href='javascript:;' class='modal_open' data-url='". route('send_command.create') ."' data-id='{$item['id']}' data-modal='send_command'>". trans('front.send_command') ."</a></div>";
                                }

                                if ( Auth::User()->perm('devices', 'edit') ) {
                                    $popup .= "<div><a href='javascript:;' class='item-edit modal_open' data-url='". route('devices.edit', [$item['id'], 0]) ."' data-modal='devices_edit'>". trans('global.edit') ."</a></div>";
                                }
                                ?>

                                <div class="edit" data-toggle="popover" data-container=".tab-objects-list" data-placement="auto" data-html="true" data-content="{!! $popup !!}" data-original-title="" title="">
                                    <a href="#">
                                        <span class="icon gear"></span>
                                    </a>
                                </div>

                            </li>

                            {{--<div class="moreinfo nonactive">
                                <div class="sub_moreinfo lat"><span> {{ trans('front.latitude') }}: </span> <span class="value"></span></div>
                                <div class="sub_moreinfo lng"><span> {{ trans('front.longitude') }}: </span> <span class="value"></span></div>
                                <div class="sub_moreinfo altitude"><span> {{ trans('front.altitude') }}: </span> <span class="value"></span></div>
                                <div class="sub_moreinfo protocol"><span> {{ trans('front.protocol') }}: </span> <span class="value"></span></div>
                                <div class="sub_moreinfo driver"><span> {{ trans('front.driver') }}: </span> <span class="value"></span></div>

                                <a href="#"><span class="collapse_button_moreinfo show-address"> {!! trans('front.show_address') !!} </span></a>
                                <a href="#"><span class="collapse_button_moreinfo close-btn"> &#94; {!! trans('front.collapse') !!} </span></a>
                                <div style="clear: both;"></div>
                            </div>--}}
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endforeach
@else
    <p style="font-size: 0.8em; padding: 12px 14px 10px 10px;">{!! trans('front.no_devices') !!}</p>
@endif
