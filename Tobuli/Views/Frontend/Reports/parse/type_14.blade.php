@extends('Frontend.Reports.parse.layout')

@section('content')
    <?php $line = 0; ?>
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
        <?php $line += 3; ?>
            <div class="panel-body no-padding">
                <table class="table table-striped table-speed" style="margin-bottom: 0px">
                    <thead>
                    <tr>
                        <th>{{ rtl(trans('front.driver'), $data) }}</th>
                        <th>{{ rtl(trans('front.distance_driver'), $data) }}</th>
                        <th>{{ rtl(trans('front.overspeed_duration'), $data) }} (s)</th>
                        <th>{{ rtl(trans('front.overspeed_score'), $data) }}</th>
                        <th>{{ rtl(trans('front.harsh_acceleration_count'), $data) }}</th>
                        <th>{{ rtl(trans('front.harsh_acceleration_score'), $data) }}(/100kms)</th>
                        <th>{{ rtl(trans('front.harsh_braking_count'), $data) }}</th>
                        <th>{{ rtl(trans('front.harsh_braking_score'), $data) }}(/100kms)</th>
                        <th>Total Score</th>
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
                        <?php $line += 1; ?>
                        @foreach ($device_items as $item)
                            <?php
                            $line += 1;
                            $distance = float($item['distance']);

                            $overspeed_score = $item['time'] > 0 && $distance > 0 ? float($item['time']/10/$distance*100) : 0;
                            $harsh_accl_score = $item['ha'] > 0 && $distance > 0 ? float($item['ha']/$distance*100) : 0;
                            $harsh_braking_score = $item['hb'] > 0 && $distance > 0 ? float($item['hb']/$distance*100) : 0;
                            $rag = $overspeed_score + $harsh_accl_score + $harsh_braking_score;
                            if($rag > 300) {
                            	$rag = 100;
                            }
                    
                    		else {
                            	$rag = round(($rag/300) * 100);
                            }

                            ?>
                            @if ($data['format'] == 'xls')
                                <tr style="background-color: {{ $rag > 95 ? '#FF0000' : ($rag < 50 ? '#00d400' : '#FFFF00') }}; color: #000000;">
                                    <td style="text-align: center;">{{ !empty($item['name']) ? $item['name'] : '-' }}</td>
                                    <td style="text-align: center;">{{ $distance }}</td>
                                    <td style="text-align: center;">{{ $item['time'] }}</td>
                                    <td style="text-align: center;">{{ $overspeed_score }}</td>
                                    <td style="text-align: center;">{{ $item['ha'] }}</td>
                                    <td style="text-align: center;">{{ $harsh_accl_score }}</td>
                                    <td style="text-align: center;">{{ $item['hb'] }}</td>
                                    <td style="text-align: center;">{{ $harsh_braking_score }}</td>
                                    <td style="text-align: center;">{{ $rag }}</td>
                                </tr>
                            @else
                                <tr style="background-color: {{ $rag > 95 ? '#FF0000' : ($rag < 50 ? '#00d400' : '#FFFF00') }}; color: #000000;">
                                    <td style="text-align: center;">{{ !empty($item['name']) ? rtl($item['name'], $data) : '-' }}</td>
                                    <td style="text-align: center;">{{ $distance }}</td>
                                    <td style="text-align: center;">{{ $item['time'] }}</td>
                                    <td style="text-align: center;">{{ $overspeed_score }}</td>
                                    <td style="text-align: center;">{{ $item['ha'] }}</td>
                                    <td style="text-align: center;">{{ $harsh_accl_score }}</td>
                                    <td style="text-align: center;">{{ $item['hb'] }}</td>
                                    <td style="text-align: center;">{{ $harsh_braking_score }}</td>
                                    <td style="text-align: center;">{{ $rag }}</td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        <?php $line += 4; ?>
    @endforeach
    <div class="panel panel-default">
        <div class="panel-body no-padding" style="padding: 0px;">
            <table class="table " style="color: #000000; margin-bottom: 0px">
                <tbody>
                <tr>
                    <td style="background-color: #FF0000;">{{ rtl(strtoupper(trans('front.above')), $data) }} 95</td>
                </tr>
                <tr>
                    <td style="background-color: #FFFF00;">From 50 to 95</td>
                </tr>
                <tr>
                    <td style="background-color: #00d400;">{{ rtl(strtoupper(trans('front.less_than')), $data) }} 50</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    @if ($data['format'] != 'xls')
    <div class="panel panel-default">
        <div class="panel-body no-padding" style="padding: 0px;">
            <table class="table" style="table-layout: auto; margin-bottom: 0px;">
                <tbody>
                <tr>
                    <td style="width: 150px;">D</td>
                    <td>{{ rtl(trans('front.distance_driver'), $data) }}</td>
                </tr>
                <tr>
                    <td style="width: 150px;">OD</td>
                    <td>{{ rtl(trans('front.overspeed_duration'), $data) }}</td>
                </tr>
                <tr>
                    <td style="width: 150px;">AC</td>
                    <td>{{ rtl(trans('front.harsh_acceleration_count'), $data) }}</td>
                </tr>
                <tr>
                    <td style="width: 150px;">AS = AC / D * 100</td>
                    <td>{{ rtl(trans('front.harsh_acceleration_score'), $data) }}</td>
                </tr>
                <tr>
                    <td style="width: 150px;">BC</td>
                    <td>{{ rtl(trans('front.harsh_braking_count'), $data) }}</td>
                </tr>
                <tr>
                    <td style="width: 150px;">BS = BC / D * 100</td>
                    <td>{{  rtl(trans('front.harsh_braking_score'), $data) }}</td>
                </tr>
                <tr>
                    <td style="width: 150px;">OS = OD / 10 / D * 100</td>
                    <td>{{ rtl(trans('front.overspeed_score'), $data) }}</td>
                </tr>
                <tr>
                    <td style="width: 150px;">R = OS + AS + BS. If R > 300, R = 100 Else (R/300) * 100</td>
                    <td>Driver Performance</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif
@stop