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
            <div class="panel-body no-padding">
                <table class="table table-striped table-speed" style="margin-bottom: 0px">
                    <thead>
                    <tr>
                        <th>{{ rtl(trans('validation.attributes.date'), $data) }}</th>
                        <th>{{ rtl(trans('front.stop_duration'), $data) }}</th>
                        <th>{{ rtl(trans('front.idle_duration'), $data) }}</th>
                        <th>{{ rtl(trans('front.engine_hours'), $data) }}</th>
                        <th>{{ rtl(trans('front.travel_time'), $data) }}</th>
                        <th>{{ rtl(trans('front.overspeed'), $data) }}</th>
                        <th>{{ rtl(trans('front.distance_travelled'), $data) }}</th>
                        <th>{{ rtl(trans('front.travel_start_time'), $data) }}</th>
                        <th>{{ rtl(trans('front.travel_end_time'), $data) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if (!isset($items[$device['id']]) || empty($items[$device['id']]))
                        <tr>
                            <td colspan="9">{{ rtl(trans('front.nothing_found_request'), $data) }}</td>
                        </tr>
                    @else
                        <?php
                        $device_items = $items[$device['id']];
                        ?>
                        @foreach ($device_items as $item)
                        <tr>
                            <td>{{ $item['date'] }}</td>
                            <td>{{ $item['stop_duration'] }}</td>
                            <td>{{ $item['engine_idle'] }}</td>
                            <td>{{ $item['engine_work'] }}</td>
                            <td>{{ $item['move_duration'] }}</td>
                            <td class="bg-danger text-l" style="color: white">{{ $item['overspeed_count'] }}</td>
                            <td>{{ $data['unit_of_distance'] == 'mi' ? kilometersToMiles($item['distance']) : $item['distance'] }} {{ rtl(trans("front.{$data['unit_of_distance']}"), $data) }}</td>
                            <td>{{ $item['start'] }}</td>
                            <td>{{ $item['end'] }}</td>
                        </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@stop