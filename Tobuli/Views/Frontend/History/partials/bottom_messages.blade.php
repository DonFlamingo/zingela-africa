<style>
.table-fixed {
    background-color: #fbfbfb;
    width: 100%;
}
.table-fixed tbody {
    height: 200px;
    overflow-y: auto;
    width: 100%;
}
.table-fixed thead, .table-fixed tbody, .table-fixed tr, .table-fixed td, .table-fixed th {
    display: block;
}
.table-fixed tbody td {
    float: left;
}
.table-fixed thead tr th {
    background-color:#159bd0;
    border-color:#0881b1;
    float: left;
      color:#fff;
}
</style>

<div class="scrollbox">
    <table class="table table-list" id="history-table-content-table" data-toggle="multiCheckbox">
        <thead>
        <tr>
            @if (Auth::User()->perm('history', 'remove'))
            {!! tableHeaderCheckall(['delete_url' => trans('admin.delete_selected')]) !!}
            @endif
            <th id="table-th-span-time" class="sorting {!! (isset($sorting) && $sorting == 'desc') ? 'sorting_desc' : 'sorting_asc'!!}">{!!trans('front.time')!!}</th>
            <th>{!!trans('front.latitude')!!}</th>
            <th>{!!trans('front.longitude')!!}</th>
            <th>{!!trans('front.altitude')!!}</th>
            <th>{!!trans('front.speed')!!}</th>
            @foreach($parameters as $param => $el)
                <th>{{$param}}</th>
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
                    <td>{!!$message['latitude']!!}</td>
                    <td>{!!$message['longitude']!!}</td>
                    <td>{!!$message['altitude']!!}</td>
                    <td>{!!$message['speed']!!}</td>
                    @foreach($parameters as $param => $el)
                        <td>@if (isset($message['other_array'][$param])) {{ $message['other_array'][$param] }} @endif</td>
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
    {!! $messages->setPath(route('history.positions'))->render() !!}
</div>



