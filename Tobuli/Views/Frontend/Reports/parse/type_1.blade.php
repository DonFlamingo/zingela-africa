@extends('Frontend.Reports.parse.layout')

@section('content')
    @foreach ($devices as $device)
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="report-bars"></div>
                {{ rtl(trans('front.report_type'), $data) }}: {{ rtl($types[$data['type']], $data) }} ({{ $data['date_from'] }} - {{ $data['date_to'] }})
            </div>
            <div class="panel-body">
                <table style="margin-bottom: 0;" class="table">
                    <tr>
                        <td>
                        <table class="table">
                            <tbody>
                            <tr>
                                <th>{{ rtl(trans('validation.attributes.device_id'), $data) }}:</th>
                                <td>{{ rtl($device['name'], $data) }}</td>
                            </tr>
                            @if (!isset($items[$device['id']]))
                                <tr>
                                    <td colspan="2">{{ rtl(trans('front.nothing_found_request'), $data) }}</td>
                                </tr>
                            @else
                                <tr>
                                    <th>{{ rtl(trans('front.route_start'), $data) }}:</th>
                                    <td>{{ $items[$device['id']]->route_start }}</td>
                                </tr>
                                <tr>
                                    <th>{{ rtl(trans('front.route_end'), $data) }}:</th>
                                    <td>{{ $items[$device['id']]->route_end }}</td>
                                </tr>
                                <tr>
                                    <th>{{ rtl(trans('front.route_length'), $data) }}:</th>
                                    <td>{{ $items[$device['id']]->unit_of_distance == 'mi' ? kilometersToMiles($items[$device['id']]->distance_sum) : $items[$device['id']]->distance_sum }} {{ rtl(trans('front.'.$items[$device['id']]->unit_of_distance), $data) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ rtl(trans('front.move_duration'), $data) }}:</th>
                                    <td>{{ $items[$device['id']]->move_duration }}</td>
                                </tr>
                                <tr>
                                    <th>{{ rtl(trans('front.stop_duration'), $data) }}:</th>
                                    <td>{{ $items[$device['id']]->stop_duration }}</td>
                                </tr>
                                <tr>
                                    <th>{{ rtl(trans('front.top_speed'), $data) }}:</th>
                                    <td>{{ $items[$device['id']]->top_speed }} {{ rtl(trans("front.dis_h_{$items[$device['id']]->unit_of_distance}"), $data) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ rtl(trans('front.average_speed'), $data) }}:</th>
                                    <td>{{ $items[$device['id']]->average_speed }} {{ rtl(trans("front.dis_h_{$items[$device['id']]->unit_of_distance}"), $data) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ rtl(trans('front.overspeed_count'), $data) }}:</th>
                                    <td>{{ $items[$device['id']]->overspeed_count }}</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </td>
                    <td>
                        @if (isset($items[$device['id']]))
                        <table class="table">
                            <tbody>
                            @if ($device['fuel_per_km'] > 0)
                                <tr>
                                    <th>{{ rtl(trans('front.fuel_consumption'), $data) }} ({{ rtl(trans('front.gps'), $data) }}):</th>
                                    <td>{{ $device['fuel_measurement_id'] == 1 ? float($items[$device['id']]->distance_sum * $device['fuel_per_km']).' '.rtl(trans('front.liters'), $data) : float(litersToGallons($items[$device['id']]->distance_sum * $device['fuel_per_km'])).' '.rtl(trans('front.gallons'), $data) }}</td>
                                </tr>
                            @endif
                            @foreach($items[$device['id']]->fuel_consumption as $id => $value)
                                <tr>
                                    <th>{{ rtl(trans('front.fuel_consumption'), $data) }} ({{ $items[$device['id']]->sensors_arr[$id]['name'] }}):</th>
                                    <td>{{ float($value).' '.$items[$device['id']]->sensors_arr[$id]['sufix'] }}</td>
                                </tr>
                            @endforeach
                            @if ($device['fuel_price'] > 0)
                                @if ($device['fuel_per_km'] > 0)
                                    <tr>
                                        <?php $fuel_consumption = $device['fuel_measurement_id'] == 1 ? $items[$device['id']]->distance_sum * $device['fuel_per_km'] : litersToGallons($items[$device['id']]->distance_sum * $device['fuel_per_km']); ?>
                                        <th>{{ rtl(trans('front.fuel_cost'), $data) }} ({{ rtl(trans('front.gps'), $data) }}):</th>
                                        <td>{{ float($fuel_consumption * $device['fuel_price']) }}</td>
                                    </tr>
                                @endif
                                @foreach($items[$device['id']]->fuel_consumption as $id => $value)
                                    <tr>
                                        <th>{{ rtl(trans('front.fuel_cost'), $data) }} ({{ $items[$device['id']]->sensors_arr[$id]['name'] }}):</th>
                                        <td>{{ float($value * $device['fuel_price']) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            <tr>
                                <th>{{ rtl(trans('front.engine_work'), $data) }}:</th>
                                <td>{{ $items[$device['id']]->engine_work }}</td>
                            </tr>
                            <tr>
                                <th>{{ rtl(trans('front.engine_idle'), $data) }}:</th>
                                <td>{{ $items[$device['id']]->engine_idle }}</td>
                            </tr>
                            @if (!is_null($items[$device['id']]->odometer))
                                <tr>
                                    <th>{{ rtl(trans('front.odometer'), $data) }}:</th>
                                    <td>{{ $items[$device['id']]->odometer }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>{{ rtl(trans('validation.attributes.engine_hours'), $data) }}:</th>
                                <td>{{ $items[$device['id']]->engine_hours }}</td>
                            </tr>
                            @if (!is_null($items[$device['id']]->getDrivers()))
                                <tr>
                                    <th>{{ rtl(trans('front.drivers'), $data) }}:</th>
                                    <td>{{ $items[$device['id']]->getDrivers() }}</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                        @endif
                    </td></tr>
                </table>
            </div>
        </div>
    @endforeach
@stop