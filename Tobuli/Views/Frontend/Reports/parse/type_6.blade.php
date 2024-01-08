@extends('Frontend.Reports.parse.layout')

@section('content')
    @foreach ($devices as $device)
        <div class="panel panel-default">
            <div class="panel-heading">
                {{ rtl(trans('front.report_type'), $data) }}: {{ rtl($types[$data['type']], $data) }}
                <div class="pull-right">
                    <small>{{ rtl(trans('front.underspeeds_count'), $data) }}: {{ isset($items[$device['id']]) ? $items[$device['id']]->underspeeds_count : 0 }}</small>
                </div>
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
                        <th>{{ rtl(trans('front.start'), $data) }}</th>
                        <th>{{ rtl(trans('front.end'), $data) }}</th>
                        <th>{{ rtl(trans('front.duration'), $data) }}</th>
                        <th>{{ rtl(trans('front.top_speed'), $data) }}</th>
                        <th>{{ rtl(trans('front.average_speed'), $data) }}</th>
                        <th>{{ rtl(trans('front.position'), $data) }}</th>
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
                        ?>
                        @if (empty($items[$device['id']]->underspeeds_count))
                            <tr>
                                <td colspan="20">{{ rtl(trans('front.nothing_found_request'), $data) }}</td>
                            </tr>
                        @else
                            @foreach ($device_items as $item)
                                @if ($item['status'] == 1 && count($item['underspeeds']))
                                    @foreach ($item['underspeeds'] as $overspeed)
                                        <tr class="text_center">
                                            <td>{{ $overspeed['start'] }}</td>
                                            <td>{{ isset($overspeed['end']) ? $overspeed['end'] : '-' }}</td>
                                            <td>{{ secondsToTime($overspeed['time']) }}</td>
                                            <td>{{ $overspeed['top_speed'] }} {{ rtl(trans("front.dis_h_{$items[$device['id']]->unit_of_distance}"), $data) }}</td>
                                            <td>{{ $overspeed['average_speed'] }} {{ rtl(trans("front.dis_h_{$items[$device['id']]->unit_of_distance}"), $data) }}</td>
                                            <td><a href="http://maps.google.com/maps?q={{ $overspeed['position']['lat'] }},{{ $overspeed['position']['lng'] }}&t=m" target="_blank">{{ $overspeed['position']['lat'] }} &deg;, {{ $overspeed['position']['lng'] }} &deg;</a>
                                                @if(!empty($overspeed['position']['address']))
                                                    - {{ rtl($overspeed['position']['address'], $data) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@stop