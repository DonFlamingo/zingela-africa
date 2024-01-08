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
                        <th>{{ rtl(trans('validation.attributes.name'), $data) }}</th>
                        <th>{{ rtl(trans('validation.attributes.quantity'), $data) }}</th>
                        <th>{{ rtl(trans('validation.attributes.cost'), $data) }}</th>
                        <th>{{ rtl(trans('validation.attributes.supplier'), $data) }}</th>
                        <th>{{ rtl(trans('validation.attributes.buyer'), $data) }}</th>
                        <th>{{ rtl(trans('validation.attributes.odometer'), $data) }}</th>
                        <th>{{ rtl(trans('validation.attributes.engine_hours'), $data) }}</th>
                        <th>{{ rtl(trans('validation.attributes.description'), $data) }}</th>
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
                                <td>{{ $item['date'] }}</td>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['quantity'] }}</td>
                                <td>{{ $item['cost'] }}</td>
                                <td>{{ $item['supplier'] }}</td>
                                <td>{{ $item['buyer'] }}</td>
                                <td>{{ $item['odometer'] }}</td>
                                <td>{{ $item['engine_hours'] }}</td>
                                <td>{{ $item['description'] }}</td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@stop