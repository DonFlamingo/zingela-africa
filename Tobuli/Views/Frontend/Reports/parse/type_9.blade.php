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
                        <th>{{ rtl(trans('front.name'), $data) }}</th>
                        <th>{{ rtl(trans('front.odometer'), $data) }}</th>
                        <th>{{ rtl(trans('front.odometer_left'), $data) }}</th>
                        <th>{{ rtl(trans('front.engine_hours'), $data) }}</th>
                        <th>{{ rtl(trans('front.engine_hours_left'), $data) }}</th>
                        <th>{{ rtl(trans('front.days'), $data) }}</th>
                        <th>{{ rtl(trans('front.days_left'), $data) }}</th>
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
                            <tr class="text_center">
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['odometer'] }}</td>
                                <td>{{ $item['odometer_left'] }}</td>
                                <td>{{ $item['engine_hours'] }}</td>
                                <td>{{ $item['engine_hours_left'] }}</td>
                                <td>{{ $item['days'] }}</td>
                                <td>{{ $item['days_left'] }}</td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@stop