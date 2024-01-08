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
                            <th>{{ rtl(trans('front.time'), $data) }}</th>
                            <th>{{ rtl(trans('front.event'), $data) }}</th>
                            <th>{{ rtl(trans('front.position'), $data) }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!isset($items[$device['id']]) || empty($items[$device['id']]))
                            <tr>
                                <td colspan="20">{{ rtl(trans('front.nothing_found_request'), $data) }}</td>
                            </tr>
                        @else
                            <?php
                            $total = [];
                            $device_items = $items[$device['id']];
                            ?>
                            @foreach ($device_items as $item)
                                <?php
                                $message = $item['message'].(isset($item['geofence']['name']) ? ' ('.$item['geofence']['name'].')' : '');
                                if (isset($total[$message]))
                                    $total[$message]++;
                                else
                                    $total[$message] = 1;
                                ?>
                                <tr class="text_center">
                                    <td>{{ $item['time'] }}</td>
                                    <td>{{ rtl($message, $data) }}</td>
                                    <td><a href="http://maps.google.com/maps?q={{ $item['latitude'] }},{{ $item['longitude'] }}&t=m" target="_blank">{{ $item['latitude'] }} &deg;, {{ $item['longitude'] }} &deg;</a>
                                        @if(!empty($item['address']))
                                            - {{ rtl($item['address'], $data) }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            @if (isset($items[$device['id']]) && !empty($items[$device['id']]))
            <div class="panel-body">
                <table class="table">
                    <tbody>
                    @foreach($total as $key => $value)
                        <tr>
                            <td>{{ rtl(trans('front.total'), $data) }} {{ rtl($key, $data) }}:</td>
                            <td>{{ $value }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    @endforeach
@stop