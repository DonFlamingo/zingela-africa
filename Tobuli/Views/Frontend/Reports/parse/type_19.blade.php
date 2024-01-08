@extends('Frontend.Reports.parse.layout')

@section('content')
    @foreach ($items['items'] as $driver => $it)
        <?php
        $driver_info = \DB::table('user_drivers')->select(['name', 'rfid'])->where('id', $data['driver'])->first();
        function split_rfid($str)
        {
            $arr1 = str_split($str, 2);
            $arr_new = array_reverse($arr1);

            return implode("", $arr_new);
        }
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                {{ rtl(trans('front.report_type'), $data) }}: {{ rtl($types[$data['type']], $data) }}
            </div>

            <div class="panel-body">
                <table class="table" style="margin-bottom: 0px">
                    <tbody>
                    <tr>
                        <th>{!! rtl(trans('front.driver'), $data) !!}:</th>
                        <td>
                            {{$driver_info->name}}
                            {{--                            {{ rtl($driver, $data) }}--}}
                        </td>
                        <th>&nbsp;</th>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <th>{!! rtl(trans('front.time_period'), $data) !!}:</th>
                        <td>{{ $data['date_from'] }} - {{ $data['date_to'] }}</td>
                        <th>&nbsp;</th>
                        <td>&nbsp;</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="panel-body no-padding">
                <table class="table table-striped table-speed" style="margin-bottom: 0px">
                    <thead>
                    <tr align="center">
                        <th rowspan="2">{{ rtl(trans('validation.attributes.device_id'), $data) }}</th>
                        <th rowspan="2">{{ rtl(trans('validation.attributes.status'), $data) }}</th>
                        <th rowspan="2">{{ rtl(trans('front.start'), $data) }}</th>
                        <th rowspan="2">{{ rtl(trans('front.end'), $data) }}</th>
                        <th rowspan="2">{{ rtl(trans('front.duration'), $data) }}</th>
                        <th colspan="4">{{ rtl(trans('front.stop_position'), $data) }}</th>
                        @if (isset($data['zones_instead']))
                            <th rowspan="2">{{ rtl(trans('front.geofences'), $data) }}</th>
                        @endif
                    </tr>
                    <tr align="center">
                        <th>{{ rtl(trans('front.route_length'), $data) }}</th>
                        <th>{{ rtl(trans('front.fuel_consumption'), $data) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php ksort($it['items']); ?>
                    @foreach ($it['items'] as $item)
                        <?php
                        $show_item = false;
                        reset($item['items']);
                        $first_key = substr(key($item['items']), 1);
                        $data_data = \DB::table('tracking_traccar.positions_' . $item['device'])->where('id', $first_key)->first();
                        $xml = new SimpleXMLElement($data_data->other);
                        $data_driver = trim(strtolower(split_rfid($xml->driverUniqueId)));
                        $data_driver_i =  trim($driver_info->rfid);
//                        echo "<pre>" . print_r($data_driver, true) . "</pre>";
//                        echo "<pre>" . print_r($data_driver_i, true) . "</pre>";
//                        var_dump($data_driver == $data_driver_i);
                        if($data_driver == $data_driver_i){
                            $show_item = true;
                        }
                        ?>
                        <?php $device = $items['devices'][$item['device']]; ?>
                        @if($show_item)

                            <tr>
                                @if ($item['status'] == 1)
                                    <td>{{ rtl($device['name'], $data) }}</td>
                                    <td>{{ rtl(trans('front.moving'), $data) }}</td>
                                    <td>{{ $item['show'] }}</td>
                                    <td>{{ $item['left'] }}</td>
                                    <td>{{ $item['time'] }}</td>
                                    <td>{{ $items['data']['unit_of_distance'] == 'mi' ? kilometersToMiles($item['distance']) : $item['distance'] }} {{ trans('front.'.$items['data']['unit_of_distance']) }}</td>
                                    <td>
                                        @if ($item['fuel_consumption'] == 0 && $device['fuel_per_km'] > 0)
                                            {{ $device['fuel_measurement_id'] == 1 ? float($item['distance'] * $device['fuel_per_km']).' '.trans('front.liters') : number_format(litersToGallons(($item['distance'] * $device['fuel_per_km'])), 2, '.', '').' '.trans('front.gallons') }}
                                        @else
                                            {{ float($item['fuel_consumption']).' '.($device['fuel_measurement_id'] == 1 ? trans('front.liters') : trans('front.gallons')) }}
                                        @endif
                                    </td>
                                @endif
                                @if ($item['status'] == 2)
                                    <td>{{ rtl($device['name'], $data) }}</td>
                                    <td>{{ rtl(trans('front.stopped'), $data) }}</td>
                                    <td>{{ $item['show'] }}</td>
                                    <td>{{ $item['left'] }}</td>
                                    <td>{{ $item['time'] }}</td>
                                    <td colspan="3">
                                        <a href="http://maps.google.com/maps?q={{ $item['stop_position']['lat'] }},{{ $item['stop_position']['lng'] }}&t=m"
                                           target="_blank">{{ $item['stop_position']['lat'] }} &deg;,
                                            {{ $item['stop_position']['lng'] }} &deg;</a>
                                        @if(!empty($item['stop_position']['address']))
                                            - {{ $item['stop_position']['address'] }}
                                        @endif
                                    </td>
                                @endif
                                @if (isset($data['zones_instead']))
                                    <td>{{ array_key_exists('geofences', $item) ? $item['geofences'] : '' }}</td>
                                @endif
                            </tr>
                        @endif

                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="panel-body">
                <table style="margin-bottom: 0;" class="table">
                    <tr>
                        <td>
                            <table class="table">
                                <tr>
                                    <td>{{ rtl(trans('front.route_length'), $data) }}:</td>
                                    <td>{{ $data['unit_of_distance'] == 'mi' ? kilometersToMiles($it['total']['distance']) : $it['total']['distance'] }} {{ rtl(trans('front.'.$data['unit_of_distance']), $data) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ rtl(trans('front.move_duration'), $data) }}:</td>
                                    <td>{{ secondsToTime($it['total']['drive']) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ rtl(trans('front.stop_duration'), $data) }}:</td>
                                    <td> {{ secondsToTime($it['total']['stop']) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ rtl(trans('front.engine_work'), $data) }}:</td>
                                    <td>{{ secondsToTime($it['total']['engine_work']) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ rtl(trans('front.engine_idle'), $data) }}:</td>
                                    <td>{{ secondsToTime($it['total']['engine_idle']) }}</td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table class="table">
                                <tr>
                                    <td>{{ rtl(trans('front.fuel_consumption'), $data) }} ({{ rtl(trans('front.gps'), $data) }}):</td>
                                    <td>
                                        {{ $device['fuel_measurement_id'] == 1 ? float($it['total']['distance'] * $device['fuel_per_km']).' '.rtl(trans('front.liters'), $data) : number_format(litersToGallons(($it['total']['distance'] * $device['fuel_per_km'])), 2, '.', '').' '.rtl(trans('front.gallons'), $data) }}
                                    </td>
                                </tr>
                                @if (!empty($it['total']['fuel_sensor']))
                                    <tr>
                                        <td>{{ rtl(trans('front.fuel_consumption'), $data) }} ({{ rtl($it['total']['fuel_sensor']['name'], $data) }}):</td>
                                        <td>{{ float($it['total']['fuel']).' '.$it['total']['fuel_sensor']['sufix'] }}</td>
                                    </tr>
                                @endif
                                @if ($device['fuel_price'] > 0)
                                    @if ($device['fuel_per_km'] > 0)
                                        <tr>
                                            <?php $fuel_consumption = $device['fuel_measurement_id'] == 1 ? $it['total']['distance'] * $device['fuel_per_km'] :
                                                litersToGallons($it['total']['distance'] * $device['fuel_per_km']); ?>
                                            <td>{{ rtl(trans('front.fuel_cost'), $data) }} ({{ rtl(trans('front.gps'), $data) }}):</td>
                                            <td>{{ float($fuel_consumption * $device['fuel_price']) }}</td>
                                        </tr>
                                    @endif
                                    @if (!empty($it['total']['fuel_sensor']))
                                        <tr>
                                            <td>{{ rtl(trans('front.fuel_cost'), $data) }} ({{ rtl($it['total']['fuel_sensor']['name'], $data) }}):</td>
                                            <td>{{ float($it['total']['fuel'] * $device['fuel_price']) }}</td>
                                        </tr>
                                    @endif
                                @endif
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @endforeach
@stop
