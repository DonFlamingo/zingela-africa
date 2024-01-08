@extends('Frontend.Reports.parse.layout')

@section('content')
    @foreach ($devices as $device)
        <div class="panel panel-default">
            <div class="panel-heading">
                {{ rtl(trans('front.report_type'), $data) }}: {{ rtl($types[$data['type']], $data) }}
            </div>
            <div class="panel-body">
                <table class="table" style="margin-bottom: 0px">
                    <tbody>
                    <tr>
                        <td><strong>{{ rtl(trans('validation.attributes.device_id'), $data) }}:</strong></td>
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
            <div class="panel-body no-padding">
                <table class="table table-striped table-speed" style="margin-bottom: 0px">
                    <thead>
                    <tr>
                        <th>{{ rtl(trans('validation.attributes.date'), $data) }}</th>
                        <th>{{ rtl(trans('front.duration'), $data) }}</th>
                        <th>{{ rtl(trans('front.position_a'), $data) }}</th>
                        <th>{{ rtl(trans('front.position_b'), $data) }}</th>
                        <th>{{ rtl(trans('front.route_length'), $data) }}</th>
                        <th>{{ rtl(trans('front.driver'), $data) }}</th>
                        @if ($device['fuel_per_km'] > 0)
                            <th>{{ rtl(trans('front.fuel_consumption'), $data) }} ({{ rtl(trans('front.gps'), $data) }})</th>
                        @endif
                        @if (isset($items[$device['id']]))
                            @foreach($items[$device['id']]->fuel_consumption as $id => $value)
                                <th>{{ rtl(trans('front.fuel_consumption'), $data) }} ({{ rtl($items[$device['id']]->sensors_arr[$id]['name'], $data) }})</th>
                            @endforeach
                        @endif
                        @if ($device['fuel_price'] > 0)
                            @if ($device['fuel_per_km'] > 0)
                                <th>{{ rtl(trans('front.fuel_cost'), $data) }} ({{ rtl(trans('front.gps'), $data) }}):</th>
                            @endif
                            @if (isset($items[$device['id']]))
                                @foreach($items[$device['id']]->fuel_consumption as $id => $value)
                                    <th>{{ rtl(trans('front.fuel_cost'), $data) }} ({{ $items[$device['id']]->sensors_arr[$id]['name'] }}):</th>
                                @endforeach
                            @endif
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @if (!isset($items[$device['id']]))
                        <tr>
                            <td colspan="20">{{ rtl(trans('front.nothing_found_request'), $data) }}</td>
                        </tr>
                    @else
                        <?php
                        $device_items = $items[$device['id']]->getItems();
                        //array_shift($device_items);
                        //array_pop($device_items);
                        $is = 0;
                        ?>
                        @if (empty($device_items))
                            <tr>
                                <td colspan="20">{{ rtl(trans('front.nothing_found_request'), $data) }}</td>
                            </tr>
                        @else
                            @foreach ($device_items as $item)
                                @if ($item['status'] == 1)
                                    <tr>
                                        <td>{{ datetime(date('Y-m-d H:i:s', strtotime($item['raw_time'])), FALSE) }}</td>
                                        <td>{{ $item['time'] }}</td>
                                        <td>
                                        <a href="http://maps.google.com/maps?q={{ $item['start_position']['lat'] }},{{ $item['start_position']['lng'] }}&t=m" target="_blank">
                                            @if(!empty($item['start_position']['address']))
                                                {{ rtl($item['start_position']['address'], $data) }}
                                            @else
                                                {{ $item['start_position']['lat'] }} &deg;, {{ $item['start_position']['lng'] }} &deg;
                                            @endif
                                        </a>
                                        </td>
                                        <td>
                                        <a href="http://maps.google.com/maps?q={{ $item['stop_position']['lat'] }},{{ $item['stop_position']['lng'] }}&t=m" target="_blank">
                                            @if(!empty($item['stop_position']['address']))
                                                {{ rtl($item['stop_position']['address'], $data) }}
                                            @else
                                                {{ $item['stop_position']['lat'] }} &deg;, {{ $item['stop_position']['lng'] }} &deg;
                                            @endif
                                        </a>
                                        </td>
                                        <td>{{ $items[$device['id']]->unit_of_distance == 'mi' ? kilometersToMiles($item['distance']) : $item['distance'] }} {{ rtl(trans('front.'.$items[$device['id']]->unit_of_distance), $data) }}</td>
                                        <td>{{ rtl($item['driver'], $data) }}</td>
                                        @if ($device['fuel_per_km'] > 0)
                                            <td>{{ $device['fuel_measurement_id'] == 1 ? float($item['distance'] * $device['fuel_per_km']).' '.rtl(trans('front.liters'), $data) : number_format(litersToGallons(($item['distance'] * $device['fuel_per_km'])), 2, '.', '').' '.rtl(trans('front.gallons'), $data) }}</td>
                                        @endif
                                        @foreach($items[$device['id']]->fuel_consumption as $id => $value)
                                            <td>{{ ($value > 0 ? float((float($value)/$items[$device['id']]->distance_sum) * $item['distance']) : float($value)).' '.rtl($items[$device['id']]->sensors_arr[$id]['sufix'], $data) }}</td>
                                        @endforeach

                                        @if ($device['fuel_price'] > 0)
                                            @if ($device['fuel_per_km'] > 0)
                                                <?php $fuel_consumption = $device['fuel_measurement_id'] == 1 ? $item['distance'] * $device['fuel_per_km'] : litersToGallons($item['distance'] * $device['fuel_per_km']); ?>
                                                <td>{{ float($fuel_consumption * $device['fuel_price']) }}</td>
                                            @endif
                                            @foreach($items[$device['id']]->fuel_consumption as $id => $value)
                                                <?php $value = $value > 0 ? float((float($value)/$items[$device['id']]->distance_sum) * $item['distance']) : float($value); ?>
                                                <td>{{ float($value * $device['fuel_price']) }}</td>
                                            @endforeach
                                        @endif
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                    @endif
                    </tbody>
                </table>
            </div>
            @if (isset($items[$device['id']]))
            <div class="panel-body">
                <table style="margin-bottom: 0;" class="table">
                    <tr>
                        <th style="width: 260px;">{{ rtl(trans('front.route_length'), $data) }}:</th>
                        <td>{{ $items[$device['id']]->unit_of_distance == 'mi' ? kilometersToMiles($items[$device['id']]->distance_sum) : $items[$device['id']]->distance_sum }} {{ rtl(trans('front.'.$items[$device['id']]->unit_of_distance), $data) }}</td>
                    </tr>
                    <tr>
                        <th style="width: 260px;">{{ rtl(trans('front.move_duration'), $data) }}:</th>
                        <td>{{ $items[$device['id']]->move_duration }}</td>
                    </tr>
                </table>
            </div>
            @endif
        </div>
    @endforeach
@stop