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
                        <th>{{ rtl(trans('validation.attributes.device_id'), $data) }}:</th>
                        <th>{{ rtl($device['name'], $data) }}</th>
                        <th>&nbsp;</th>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <th>{!! rtl(trans('front.time_period'), $data) !!}:</th>
                        <th>{{ $data['date_from'] }} - {{ $data['date_to'] }}</th>
                        <th>&nbsp;</th>
                        <td>&nbsp;</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            @if (!isset($items[$device['id']]) || empty($items[$device['id']]->fuel_tank_fillings))
                <div class="panel-body">
                    {{ rtl(trans('front.nothing_found_request'), $data) }}
                </div>
            @else
                <?php
                $device_items = $items[$device['id']];
                ?>
                @foreach ($device_items->fuel_tank_fillings as $sensor_id => $flitems)
                    <?php $sufix = rtl($device_items->sensors_arr[$sensor_id]['sufix'], $data); ?>
                    <div class="panel-body">
                        <table class="table" style="margin-bottom: 0px">
                            <tbody>
                            <tr>
                                <th>{{ rtl(trans('front.sensor'), $data) }} "{{ rtl($device_items->sensors_arr[$sensor_id]['name'], $data) }}"</th>
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
                                    <th>{{ rtl(trans('front.time'), $data) }}</th>
                                    <th>{{ rtl(trans('front.last_value'), $data) }}</th>
                                    <th>{{ rtl(trans('front.difference'), $data) }}</th>
                                    <th>{{ rtl(trans('front.current_value'), $data) }}</th>
                                    <th>{{ rtl(trans('front.position'), $data) }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($flitems as $item)
                                <tr>
                                    <td>{{ $item['time'] }}</td>
                                    <td>{{ $item['last'] }} {{ $sufix }}</td>
                                    <td>{{ $item['diff'] }} {{ $sufix }}</td>
                                    <td>{{ $item['current'] }} {{ $sufix }}</td>
                                    <td><a href="http://maps.google.com/maps?q={{ $item['lat'] }},{{ $item['lng'] }}&t=m" target="_blank">{{ $item['lat'] }} &deg;, {{ $item['lng'] }} &deg;</a>
                                        @if(!empty($item['address']))
                                            - {{ rtl($item['address'], $data) }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            @endif
        </div>
    @endforeach
@stop