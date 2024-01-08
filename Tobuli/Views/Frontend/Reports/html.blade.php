<!DOCTYPE html>
<html lang="en">
<head>
    <title>atrams.com</title>
    <style type="text/css">
        .logo {
            max-height: 80px;
        }
        html, body {
            text-align: left;
            margin: 10px;
            padding: 0px;
            font-size: 13px;
            font-family: Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif;
            color: #444444;
        }

        h3 {
            font-size: 15px;
            color: #444444;
            font-weight: bold;
        }

        hr {
            border-color:#cccccc;
            border-style:solid none none;
            border-width:1px 0 0;
            height:1px;
            margin-left:1px;
            margin-right:1px;
        }

        .control-buttons { margin: 0 0 0 15px; }
        .control-buttons a {
            display: inline-block;
            -webkit-transition: all 0.3s ease;
            -moz-transition: all 0.3s ease;
            -ms-transition: all 0.3s ease;
            -o-transition: all 0.3s ease;
            transition: all 0.3s ease;
        }
        .control-buttons a:hover { opacity: 0.6; }

        a:link,
        a:visited {
            text-decoration: none;
            color: #004c8c;
        }
        a:active { color: #004c8c; }
        a:hover { text-decoration: underline; color: #004c8c; }

        caption,
        th,
        td { vertical-align: middle; }

        table.report {
            color:#333333;
            border: 1px solid #eeeeee;
            border-collapse: collapse;
        }

        table.report th {
            font-weight: bold;
            padding: 2px;
            border: 1px solid #eeeeee;
            background-color: #eeeeee;
        }

        table.report td {
            padding: 2px;
            border: 1px solid #eeeeee;
        }

        table.report tr:hover { background-color: #F6F6F6; }

        td { mso-number-format:"@";/*force text*/ }

    </style>
</head>
<body>
<img src="{!!asset('frontend/images/header/'.$_ENV['server'].'-logo.png')!!}" class="logo" alt="Logo">
<hr>
@foreach ($devices as $device)
    <h3>{!!$types[$data['type']]!!}</h3>
    <table>
        <tbody>
            <tr>
                <td><strong>{!!trans('validation.attributes.device_id')!!}:</strong></td>
                <td>{!!$device['name']!!}</td>
            </tr>
            <tr>
                <td><strong>{!!trans('front.time_period')!!}:</strong></td>
                <td>{{$data['date_from']}} - {{$data['date_to']}}</td>
            </tr>
        </tbody>
    </table>
    <br>
    @if (!isset($items[$device['id']]))
        {!!trans('front.nothing_found_request')!!}
        <?php continue; ?>
    @endif
    <table>
        <tbody>
        <tr>
            <td><strong>{!!trans('front.route_start')!!}:</strong></td>
            <td>{!!$items[$device['id']]->route_start!!}</td>
        </tr>
        <tr>
            <td><strong>{!!trans('front.route_end')!!}:</strong></td>
            <td>{!!$items[$device['id']]->route_end!!}</td>
        </tr>
        <tr>
            <td><strong>{!!trans('front.route_length')!!}:</strong></td>
            <td>{!!$items[$device['id']]->distance_sum!!} {!!trans('front.km')!!}</td>
        </tr>
        <tr>
            <td><strong>{!!trans('front.move_duration')!!}:</strong></td>
            <td>{!!$items[$device['id']]->move_duration!!}</td>
        </tr>
        <tr>
            <td><strong>{!!trans('front.stop_duration')!!}:</strong></td>
            <td> {!!$items[$device['id']]->stop_duration!!}</td>
        </tr>
        <tr>
            <td><strong>{!!trans('front.top_speed')!!}:</strong></td>
            <td>{!!$items[$device['id']]->top_speed!!} {!!Auth::User()->distance_unit_hour!!}</td>
        </tr>
        <tr>
            <td><strong>{!!trans('front.average_speed')!!}:</strong></td>
            <td>{!!$items[$device['id']]->average_speed!!} {!!Auth::User()->distance_unit_hour!!}</td>
        </tr>
        <tr>
            <td><strong>{!!trans('front.overspeed_count')!!}:</strong></td>
            <td>{!!$items[$device['id']]->overspeed_count!!}</td>
        </tr>
        <tr>
            <td><strong>{!!trans('front.fuel_consumption')!!}:</strong></td>
            <td>{!!float($items[$device['id']]->distance_sum * $device['fuel_per_km'])!!} {!!trans('front.liters')!!}</td>
        </tr>
        </tbody>
    </table>
@endforeach
</body>
</html>