@extends('Frontend.Reports.parse.layout')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            {{ rtl(trans('front.report_type'), $data) }}: {{ rtl($types[$data['type']], $data) }}
        </div>
        <div class="panel-body">
            <table class="table" style="margin-bottom: 0px">
                <tbody>
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
                    <th>{{ rtl(trans('validation.attributes.device_id'), $data) }}</th>
                    <th>{{ rtl(trans('front.route_start'), $data) }}</th>
                    <th>{{ rtl(trans('front.route_end'), $data) }}</th>
                    <th>{{ rtl(trans('front.route_length'), $data) }}</th>
                    <th>{{ rtl(trans('front.move_duration'), $data) }}</th>
                    <th>{{ rtl(trans('front.stop_duration'), $data) }}</th>
                    <th>{{ rtl(trans('front.top_speed'), $data) }}</th>
                    <th>{{ rtl(trans('front.average_speed'), $data) }}</th>
                    <th>{{ rtl(trans('front.overspeed_count'), $data) }}</th>
                    <th>{{ rtl(trans('front.fuel_consumption'), $data) }}</th>
                    <th>{{ rtl(trans('front.fuel_cost'), $data) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($devices as $device)
                    @if (!isset($items[$device['id']]))
                        <tr>
                            <td>{{ rtl($device['name'], $data) }}</td>
                            <td colspan="20">{{ rtl(trans('front.nothing_found_request'), $data) }}</td>
                        </tr>
                    @else
                        <tr>
                            <td>{{ rtl($device['name'], $data) }}</td>
                            <td>{{ $items[$device['id']]->route_start }}</td>
                            <td>{{ $items[$device['id']]->route_end }}</td>
                            <td>{{ $items[$device['id']]->unit_of_distance == 'mi' ? kilometersToMiles($items[$device['id']]->distance_sum) : $items[$device['id']]->distance_sum }} {{ trans('front.'.$items[$device['id']]->unit_of_distance) }}</td>
                            <td>{{ $items[$device['id']]->move_duration }}</td>
                            <td>{{ $items[$device['id']]->stop_duration }}</td>
                            <td>{{ $items[$device['id']]->top_speed }} {{ trans("front.dis_h_{$items[$device['id']]->unit_of_distance}") }}</td>
                            <td>{{ $items[$device['id']]->average_speed }} {{ trans("front.dis_h_{$items[$device['id']]->unit_of_distance}") }}</td>
                            <td>{{ $items[$device['id']]->overspeed_count }}</td>
                            @if (empty($items[$device['id']]->fuel_consumption))
                                <td>{{ $device['fuel_measurement_id'] == 1 ? float($items[$device['id']]->distance_sum * $device['fuel_per_km']).' '.trans('front.liters') : float(litersToGallons($items[$device['id']]->distance_sum * $device['fuel_per_km'])).' '.trans('front.gallons') }}</td>
                            @else
                                <?php
                                $cons = 0;
                                foreach($items[$device['id']]->fuel_consumption as $id => $value) {
                                    $cons += $value;
                                }
                                ?>
                                <td>{{ float($cons).' '.($device['fuel_measurement_id'] == 1 ? trans('front.liters') : trans('front.gallons')) }}</td>
                            @endif
                            <td>
                                @if ($device['fuel_price'] > 0)
                                    @if (empty($items[$device['id']]->fuel_consumption))
                                        <?php $fuel_consumption = $device['fuel_measurement_id'] == 1 ? $items[$device['id']]->distance_sum * $device['fuel_per_km'] : litersToGallons($items[$device['id']]->distance_sum * $device['fuel_per_km']); ?>
                                        {{ float($fuel_consumption * $device['fuel_price']) }}
                                    @else
                                        <?php $value = array_sum($items[$device['id']]->fuel_consumption); ?>
                                        {{ float($value * $device['fuel_price']) }}
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop