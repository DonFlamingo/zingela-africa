@extends('Frontend.Reports.parse.layout')

@section('content')
    <?php
    function split_rfid($str)
    {
        $arr1 = str_split($str, 2);
        $arr_new = array_reverse($arr1);

        return implode("", $arr_new);
    }
    ?>
    @foreach ($devices as $device)
        <div class="panel panel-default">
            <div class="panel-heading">
                {{ rtl(trans('front.report_type'), $data) }}: {{ rtl($types[$data['type']], $data) }}
            </div>
            <div class="panel-body">
                <table class="table" style="margin-bottom: 0px">
                    <tbody>
                    <tr>
                        <th>{!! rtl(trans('validation.attributes.device_id'), $data) !!}:</th>
                        <td>{{ rtl($device['name'], $data) }}</td>
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
            <div class="panel-body py-5">
                <table id="datatable-{{$device['name']}}" class="table table-bordered table-striped table-speed" style="margin-bottom: 0px">
                    <thead>
                    <tr>
                        <th rowspan="2">{{ rtl(trans('validation.attributes.status'), $data) }}</th>
                        <th rowspan="2">{{ rtl(trans('front.start'), $data) }}</th>
                        <th rowspan="2">{{ rtl(trans('front.end'), $data) }}</th>
                        <th rowspan="2">{{ rtl(trans('front.duration'), $data) }}</th>
                        <th rowspan="2">{{ rtl(trans('front.engine_idle'), $data) }}</th>
                        <th rowspan="2">{{ rtl(trans('front.driver'), $data) }}</th>
                        <th colspan="4">{{ rtl(trans('front.stop_position'), $data) }}</th>
                        @if (isset($data['zones_instead']))
                            <th rowspan="2">{{ rtl(trans('front.geofences'), $data) }}</th>
                        @endif
                    </tr>
                    <tr>
                        <th>{{ rtl(trans('front.route_length'), $data) }}</th>
                        <th>{{ rtl(trans('front.top_speed'), $data) }}</th>
                        <th>{{ rtl(trans('front.average_speed'), $data) }}</th>
                        <th>{{ rtl(trans('front.fuel_consumption'), $data) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if (!isset($items[$device['id']]))
                        <tr>
                            <td colspan="20">{{ rtl(trans('front.nothing_found_request'), $data) }}</td>
                        </tr>
                    @else
                        @foreach ($items[$device['id']]->getItems() as $item)
                            <?php
                            //                                    $driver_name = \DB::table('user_drivers')->get();
                            ?>
                            <tr>
                                @if ($item['status'] == 1)
                                    <td>{{ rtl(trans('front.moving'), $data) }}</td>
                                    <td>{{ $item['show'] }}</td>
                                    <td>{{ $item['left'] }}</td>
                                    <td>{{ $item['time'] }}</td>
                                    <td>{{ secondsToTime($item['engine_idle']) }}</td>
                                    <td>
                                        @foreach ($item['items'] as $k => $it)
                                            <?php
                                            if (isset($items[$device['id']]->getCords()[$k]['other_arr'][39])) {
                                                //  echo "<pre>".print_r($items[$device['id']]->getCords()[$k]['other_arr'][39],true)."</pre>";
                                                $driver = explode(" ", $items[$device['id']]->getCords()[$k]['other_arr'][39]);
                                                $driverrfid_end = end($driver);
                                                $driverrfid = split_rfid($driverrfid_end);
                                                $driver_data = \DB::table('user_drivers')->select(['name'])->where('rfid', strtolower($driverrfid))->first();
                                                if (!empty($driver_data)) {
                                                    echo $driver_data->name;
                                                }
                                            } else {
                                                rtl($item['driver'], $data);
                                            }
                                            break;
                                            ?>
                                        @endforeach




                                        {{--                                            {{ rtl($item['driver'], $data) }}--}}


                                    </td>
                                    <td>{{ $items[$device['id']]->unit_of_distance == 'mi' ? kilometersToMiles($item['distance']) : $item['distance'] }} {{ rtl(trans('front.'.$items[$device['id']]->unit_of_distance), $data) }}</td>
                                    <td>{{ $item['top_speed'] }} {{ rtl(trans("front.dis_h_{$items[$device['id']]->unit_of_distance}"), $data) }}</td>
                                    <td>{{ $item['average_speed'] }} {{ rtl(trans("front.dis_h_{$items[$device['id']]->unit_of_distance}"), $data) }}</td>
                                    <td>
                                        @if ($item['fuel_consumption'] == 0 && $device['fuel_per_km'] > 0)
                                            {{ $device['fuel_measurement_id'] == 1 ? float($item['distance'] * $device['fuel_per_km']).' '.rtl(trans('front.liters'), $data) : number_format(litersToGallons(($item['distance'] * $device['fuel_per_km'])), 2, '.', '').' '.rtl(trans('front.gallons'), $data) }}
                                        @else
                                            {{ float($item['fuel_consumption']).' '.($device['fuel_measurement_id'] == 1 ? rtl(trans('front.liters'), $data) : rtl(trans('front.gallons'), $data)) }}
                                        @endif
                                    </td>
                                @endif
                                @if ($item['status'] == 2)
                                    <td>{{ rtl(trans('front.stopped'), $data) }}</td>
                                    <td>{{ $item['show'] }}</td>
                                    <td>{{ $item['left'] }}</td>
                                    <td>{{ $item['time'] }}</td>
                                    <td>{{ secondsToTime($item['engine_idle']) }}</td>
                                    <td>
                                        @foreach ($item['items'] as $k => $it)
                                            <?php
                                            if (isset($items[$device['id']]->getCords()[$k]['other_arr'][39])) {
                                                //  echo "<pre>".print_r($items[$device['id']]->getCords()[$k]['other_arr'][39],true)."</pre>";
                                                $driver = explode(" ", $items[$device['id']]->getCords()[$k]['other_arr'][39]);
                                                $driverrfid_end = end($driver);
                                                $driverrfid = split_rfid($driverrfid_end);
                                                $driver_data = \DB::table('user_drivers')->select(['name'])->where('rfid', strtolower($driverrfid))->first();
                                                if (!empty($driver_data)) {
                                                    echo $driver_data->name;
                                                }
                                            } else {
                                                rtl($item['driver'], $data);
                                            }
                                            break;
                                            ?>
                                        @endforeach

                                        {{--                                            {{ rtl($item['driver'], $data)  }}--}}
                                    </td>
                                    <td>
                                        <a href="http://maps.google.com/maps?q={{ $item['stop_position']['lat'] }},{{ $item['stop_position']['lng'] }}&t=m"
                                           target="_blank">{{ $item['stop_position']['lat'] }} &deg;, {{ $item['stop_position']['lng'] }} &deg;</a>
                                        @if(!empty($item['stop_position']['address']))
                                            - {{ $item['stop_position']['address'] }}
                                        @endif
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                @endif
                                @if (isset($data['zones_instead']))
                                    <td>{{ array_key_exists('geofences', $item) ? rtl($item['geofences'], $data) : '' }}</td>
                                @endif
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="panel-body">
                @if (isset($items[$device['id']]))
                    <table style="margin-bottom: 0;" class="table">
                        <tr>
                            <td>
                                <table class="table">
                                    <tr>
                                        <td>{{ rtl(trans('front.route_length'), $data) }}:</td>
                                        <td>{{ $items[$device['id']]->unit_of_distance == 'mi' ? kilometersToMiles($items[$device['id']]->distance_sum) : $items[$device['id']]->distance_sum }} {{ rtl(trans('front.'.$items[$device['id']]->unit_of_distance), $data) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ rtl(trans('front.move_duration'), $data) }}:</td>
                                        <td>{{ $items[$device['id']]->move_duration }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ rtl(trans('front.stop_duration'), $data) }}:</td>
                                        <td> {{ $items[$device['id']]->stop_duration }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ rtl(trans('front.top_speed'), $data) }}:</td>
                                        <td>{{ $items[$device['id']]->top_speed }} {{ rtl(trans('front.dis_h_'.$items[$device['id']]->unit_of_distance), $data) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ rtl(trans('front.average_speed'), $data) }}:</td>
                                        <td>{{ $items[$device['id']]->average_speed }} {{ rtl(trans('front.dis_h_'.$items[$device['id']]->unit_of_distance), $data) }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td>
                                <table class="table">
                                    <tr>
                                        <td>{{ rtl(trans('front.engine_work'), $data) }}:</td>
                                        <td>{{ $items[$device['id']]->engine_work }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ rtl(trans('front.engine_idle'), $data) }}:</td>
                                        <td>{{ $items[$device['id']]->engine_idle }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ rtl(trans('front.fuel_consumption'), $data) }} ({{ rtl(trans('front.gps'), $data) }}):</td>
                                        <td>{{ $device['fuel_measurement_id'] == 1 ? float($items[$device['id']]->distance_sum * $device['fuel_per_km']).' '.rtl(trans('front.liters'), $data) : float(litersToGallons($items[$device['id']]->distance_sum * $device['fuel_per_km'])).' '.rtl(trans('front.gallons'), $data) }}</td>
                                    </tr>
                                    @foreach($items[$device['id']]->fuel_consumption as $id => $value)
                                        <tr>
                                            <td>{{ rtl(trans('front.fuel_consumption'), $data) }} ({{ rtl($items[$device['id']]->sensors_arr[$id]['name'], $data) }}):
                                            </td>
                                            <td>{{ float($value).' '.$items[$device['id']]->sensors_arr[$id]['sufix'] }}</td>
                                        </tr>
                                    @endforeach
                                    @if ($device['fuel_price'] > 0)
                                        @if ($device['fuel_per_km'] > 0)
                                            <tr>
                                                <?php $fuel_consumption =
                                                    $device['fuel_measurement_id'] == 1 ? $items[$device['id']]->distance_sum * $device['fuel_per_km'] :
                                                        litersToGallons($items[$device['id']]->distance_sum * $device['fuel_per_km']); ?>
                                                <td>{{ rtl(trans('front.fuel_cost'), $data) }} ({{ rtl(trans('front.gps'), $data) }}):</td>
                                                <td>{{ float($fuel_consumption * $device['fuel_price']) }}</td>
                                            </tr>
                                        @endif
                                        @foreach($items[$device['id']]->fuel_consumption as $id => $value)
                                            <tr>
                                                <td>{{ rtl(trans('front.fuel_cost'), $data) }} ({{ $items[$device['id']]->sensors_arr[$id]['name'] }}):</td>
                                                <td>{{ float($value * $device['fuel_price']) }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </table>
                            </td>
                        </tr>
                    </table>
                @endif
            </div>
        </div>
    @endforeach

@stop

@section('foot-content')
    @foreach ($devices as $device)
        <script>
            $(function () {
                $("#datatable-{{$device['name']}}").DataTable({
                    paging: false,
                    "order": [[ 1, "asc" ]]
                });
            });
        </script>
    @endforeach
@stop
