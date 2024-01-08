<div class="table-responsive scrollbox">
    <table class="table table-list" id="history-table-content-table" data-toggle="multiCheckbox">
        <thead>
        <tr>
            @if (Auth::User()->perm('history', 'remove'))
            {!! tableHeaderCheckall(['delete_url' => trans('admin.delete_selected')]) !!}
            @endif
            <th id="table-th-span-time" class="sorting {!! (isset($sorting) && $sorting == 'desc') ? 'sorting_desc' : 'sorting_asc'!!}">{!!trans('front.time')!!}</th>
            <th>Address</th>
            <th>{!!trans('front.altitude')!!}</th>
            <th>{!!trans('front.speed')!!}</th>
            @foreach($sensors as $sensor)
                @if ($sensor['add_to_history'])
                    <th>{{$sensor['name']}}</th>
                @endif
            @endforeach
            <th style="display: none"></th>
        </tr>
        </thead>
        <tbody>
        @if (!empty($messages))
            @foreach($messages as $message)
                <tr data-position_id="{!!$message['id']!!}" data-lat="{!!$message['latitude']!!}" data-lng="{!!$message['longitude']!!}" data-speed="{!!$message['speed']!!}" data-altitude="{!!$message['altitude']!!}" data-time="{!!$message['time']!!}">
                    @if (Auth::User()->perm('history', 'remove'))
                    <td>
                        <div class="checkbox">
                            {!! Form::checkbox( 'history_message[]', $message['id'].'-'.$message['sensor_id'], null) !!}
                            {!! Form::label( null ) !!}
                        </div>
                    </td>
                    @endif
                    <td>{!!$message['time']!!}</td>
                    <?php $address = new \Tobuli\Helpers\GeoAddressHelper(); ?>
                    <td>{!! $address->getGeoAddress($message['latitude'], $message['longitude']) !!}</td>
                    <td>{!!$message['altitude']!!}</td>
                    <td>{!!$message['speed']!!}</td>
                    @foreach($sensors as $key => $sensor)
                        @if ($sensor['add_to_history'])
                            <td>{{isset($message['sensors_value'][$sensor['id']]) ? $message['sensors_value'][$sensor['id']] : '-'}}</td>
                        @endif
                    @endforeach
                    <td style="display: none">
                        <span class="message_other">{!!json_encode($message['other_arr'])!!}</span>
                        <span class="message_sensors">{!!json_encode($message['popup_sensors'])!!}</span>
                    </td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
</div>
<div class="nav-pagination">
    {!! $messages->setPath(route('history.history'))->render() !!}
</div>
