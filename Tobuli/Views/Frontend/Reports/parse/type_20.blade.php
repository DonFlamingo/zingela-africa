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
                            <th>{{ rtl(trans('front.zone_in'), $data) }}</th>
                            <th>{{ rtl(trans('front.zone_out'), $data) }}</th>
                            <th>{{ rtl(trans('front.duration'), $data) }}</th>
                            <th>{{ rtl(trans('validation.attributes.geofence_name'), $data) }}</th>
                            <th>{{ rtl(trans('front.ignition_on_off'), $data) }}</th>
                            <th>{{ rtl(trans('front.position'), $data) }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!isset($items[$device['id']]) || empty($items[$device['id']]['items']))
                            <tr>
                                <td colspan="20">{{ rtl(trans('front.nothing_found_request'), $data) }}</td>
                            </tr>
                        @else
                            <?php
                            $device_items = $items[$device['id']];
                            ?>
                            @foreach ($device_items['items'] as $item)
                                @if($item['duration_engine_on'])
                                <tr>
                                    <td>{{ $item['start'] }}</td>
                                    <td>{{ $item['end'] }}</td>
                                    <td>{{ $item['duration_engine_on'] }}</td>
                                    <td>{{ rtl($item['name'], $data) }}</td>
                                    <td>{{ rtl(trans('front.on'), $data) }}</td>
                                    <td><a href="http://maps.google.com/maps?q={{ $item['position']['lat'] }},{{ $item['position']['lng'] }}&t=m" target="_blank">{{ $item['position']['lat'] }} &deg;, {{ $item['position']['lng'] }} &deg;</a>
                                        @if(!empty($item['position']['address']))
                                            - {{ rtl($item['position']['address'], $data) }}
                                        @endif
                                    </td>
                                </tr>
                                @endif

                                @if($item['duration_engine_off'])
                                <tr>
                                    <td>{{ $item['start'] }}</td>
                                    <td>{{ $item['end'] }}</td>
                                    <td>{{ $item['duration_engine_off'] }}</td>
                                    <td>{{ rtl($item['name'], $data) }}</td>
                                    <td>{{ rtl(trans('front.off'), $data) }}</td>
                                    <td><a href="http://maps.google.com/maps?q={{ $item['position']['lat'] }},{{ $item['position']['lng'] }}&t=m" target="_blank">{{ $item['position']['lat'] }} &deg;, {{ $item['position']['lng'] }} &deg;</a>
                                        @if(!empty($item['position']['address']))
                                            - {{ rtl($item['position']['address'], $data) }}
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                            <tr>
                                <td colspan="20"></td>
                            </tr>

                            @foreach ($device_items['totals'] as $item)
                            <tr>
                                <td></td>
                                <td>{{ rtl(trans('front.total'), $data) }} {{ rtl($item['name'], $data) }} / {{ rtl(trans('front.ignition'), $data) }} {{ rtl(trans('front.on'), $data) }}</td>
                                <td>{{ $item['duration_engine_on'] }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@stop