@extends('Frontend.Reports.parse.layout')

@section('scripts')
    <script src="{{ asset('assets/js/core.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <style>
        .demo-placeholder {
            width: 100%;
            height: 150px;
            font-size: 14px;
            line-height: 1.2em;
        }
        .graph-control {
            height: 40px;
        }
        .graph-control-label {
            float: right;
        }
        .graph-control {
            height: 27px;
        }
        .graph-control li {
            display: inline;
        }
        .graph-control-buttons {
            float: right;
            margin-left: 10px;
        }
        .graph-control-buttons img {
            cursor: pointer;
        }
    </style>
    <script>
        var plots = {};
        var options = {
            colors: ["rgba(76, 84, 84, 1)"],
            series: {
                shadowSize: 0
            },
            crosshair: {
                mode: "x"
            },
            lines: {
                show: true,
                lineWidth: 1.5,
                lineColor: 'red',
                fill: true,
                fillColor: "rgba(76, 84, 84, 0.4)"

                // steps: e
            },
            /*selection: {
             mode: "x"
             },*/
            zoom: {
                interactive: false
            },
            pan: {
                interactive: true
            },
            xaxis: {
                minTickSize: [30, "minute"],
                mode: 'time',
                twelveHourClock: false,
            },
            yaxis: {
                tickFormatter: function(value, axis) { return value.toFixed(axis.tickDecimals) + 'L'; },
                minTickSize: 1,
                tickDecimals: 0,
                zoomRange: false

            },
            legend: {
                noColumns: 0,
                labelFormatter: function (label, series) {
                    return "<font color=\"white\">" + label + "</font>";
                },
                backgroundColor: "#000",
                backgroundOpacity: 1.0,
                labelBoxBorderColor: "#000000",
                position: "nw"
            },
            grid: {
                color: "#999999",
                margin: {
                    top: 10,
                    left: 10,
                    bottom: 10,
                    right: 10
                },
                hoverable: true,
                borderWidth: 1,
                borderColor: '#DDDDDD'
            }

        };
    </script>
@stop

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
            <div class="panel-body">
                @if (!isset($items[$device['id']]) || empty($data['sensors_var']) || empty($items[$device['id']]->{$data['sensors_var']}))
                    <span style="padding-left: 16px">{{ trans('front.nothing_found_request') }}</span>
                @else
                    <?php
                    $device_items = $items[$device['id']];
                    $sensors_values = $device_items->getSensorsValues();
                    ?>
                    @foreach($items[$device['id']]->{$data['sensors_var']} as $sensor_id)
                    @if($data['format'] != 'xls')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="graph-1-wrap">
                                    <div class="graph-control">
                                        <li><strong>{{ trans('front.sensor') }}:</strong> {{ $items[$device['id']]->sensors_arr[$sensor_id]['name'] }}</li>
                                        <li class="graph-control-buttons" id="{{ $sensor_id }}-panRight" style="padding-top: 4px"><img src="{{ asset('assets/img/arrow_right.gif') }}" border="0"></li>
                                        <li class="graph-control-buttons" id="{{ $sensor_id }}-panLeft" style="padding-top: 4px"> <img src="{{ asset('assets/img/arrow_left.gif') }}" border="0"></li>
                                        <li class="graph-control-buttons" id="{{ $sensor_id }}-zoomIn"> <img src="{{ asset('assets/img/zoom_in.png') }}" style="margin-top:4px;" border="0"></li>
                                        <li class="graph-control-buttons" id="{{ $sensor_id }}-zoomOut"><img src="{{ asset('assets/img/zoom_out.png') }}" style="margin-top:4px;" border="0"></li>
                                        <li class="graph-control-label" id="{{ $sensor_id }}-labeler">
                                            <div style="padding-top: 5px">
                                                <span id="{{ $sensor_id }}-hoverdata-kmh" style="font-weight: bold"></span>
                                                <span id="{{ $sensor_id }}-hoverdata-date"></span>
                                            </div>
                                        </li>
                                    </div>
                                    <div id="{{ $sensor_id }}-placeholder" class="demo-placeholder">

                                    </div>
                                    <script>
                                        options.yaxis.tickFormatter = function(value, axis) {
                                            return value.toFixed(axis.tickDecimals) + '{{ $items[$device['id']]->sensors_arr[$sensor_id]['sufix'] }}';
                                        };
                                        var items = {!! json_encode($sensors_values[$sensor_id]) !!};
                                        var graph_items = [];
                                        $.each(items, function(index, value) {
                                            var date = new Date(value.t.substring(0, 10) + 'T' + value.t.substring(11, 19) + '.000Z');
                                            graph_items.push([date, value.v, 0, index]);
                                        });
                                        plots['{{ $sensor_id }}'] = $.plot("#{{ $sensor_id }}-placeholder", [graph_items], options);

                                        $("#{{ $sensor_id }}-placeholder").bind("plothover", function (event, pos, item) {
                                            if (item != null) {
                                                var strKmh = item.datapoint[1] + " {{ $items[$device['id']]->sensors_arr[$sensor_id]['sufix'] }}";
                                                var fixDate = moment(item.datapoint[0]).subtract({hours: 3}).tz('Europe/Vilnius').format('YYYY-MM-DD HH:mm:ss');

                                                $("#{{ $sensor_id }}-hoverdata-kmh").text(strKmh);
                                                $("#{{ $sensor_id }}-hoverdata-date").text(' - ' + fixDate);
                                            }
                                        });

                                        $("#{{ $sensor_id }}-panLeft").on('click', function(e){
                                            e.preventDefault();
                                            plots['{{ $sensor_id }}'].pan({
                                                left: -100
                                            })
                                        });

                                        $("#{{ $sensor_id }}-panRight").on('click', function(e){
                                            e.preventDefault();
                                            plots['{{ $sensor_id }}'].pan({
                                                left: +100
                                            })
                                        });

                                        $("#{{ $sensor_id }}-zoomIn").on('click', function(e){
                                            e.preventDefault();
                                            plots['{{ $sensor_id }}'].zoom();
                                        });

                                        $("#{{ $sensor_id }}-zoomOut").on('click', function(e){
                                            e.preventDefault();
                                            plots['{{ $sensor_id }}'].zoomOut();
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
            @endif
                        <div class="row">
                            <div class="col-md-12">
                                  <table class="table" style="margin-bottom: 0px">
                                    <thead>
                                        <tr>
                                            <th>{{ $items[$device['id']]->sensors_arr[$sensor_id]['name'] }} ({{ $items[$device['id']]->sensors_arr[$sensor_id]['sufix'] }})</th>
                                            <th>Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($sensors_values[$sensor_id] as $each_sensor)
                                    <tr>
                                        <td>{{$each_sensor['v']}}</td>
                                        <td>{{$each_sensor['t']}}</td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    @endforeach
@stop