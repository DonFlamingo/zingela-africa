@if (!empty($items))
    <div class="history">
        <table class="table">
            <thead>
            <tr>
                <td><i class="fa fa-flag" title="{!! trans('front.action') !!}"></i></td>
                <td><i class="fa fa-calendar" title="{!! trans('validation.attributes.date') !!}"></i></td>
                <td><i class="fa fa-clock-o" title="{!! trans('front.duration') !!}"></i></td>
            </tr>
            </thead>
            <tbody>
            @foreach ($items as $key => $item)
                <?php $st = $item_class[$item['status']]; ?>
                <tr data-history-id="{!!$key!!}" class="{!! $st['tr'] !!}" onClick="app.history.select( {!!$key!!} );">
                    <td>
                        <span class="{{ $st['class'] }}">{!! $st['sym'] !!}</span>
                    </td>
                    <td class="datetime">
                        <span class="time">{{ date(settings('main_settings.default_time_format'), strtotime($item['raw_time'])) }}</span>
                        <span class="date">{{ date(settings('main_settings.default_date_format'), strtotime($item['raw_time'])) }}</span>
                    </td>
                    <td class="duration">
                        @if ($item['time'] != 0)
                            {{ $item['time'] }}
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <input type="hidden" id="history_distance_sum" value="{!!$distance_sum!!}">
    <input type="hidden" id="history_move_duration" value="{!!$move_duration!!}">
    <input type="hidden" id="history_stop_duration" value="{!!$stop_duration!!}">
    <input type="hidden" id="history_top_speed" value="{!!$top_speed!!}">
    <input type="hidden" id="history_fuel_consumption" value="{!!$fuel_consumption!!}">
    <script>
        window.history_fuel_consumption_arr = {!! json_encode($fuel_consumption_arr) !!};
        window.history_cords = {!!json_encode($cords)!!};
        window.history_items = {!!json_encode($items)!!};
        window.history_sensors = {!!json_encode($sensors)!!};
        window.history_sensors_values = {!!json_encode($sensors_values)!!};
    </script>
@else
    <p class="no-results">{!!trans('front.no_history')!!}</p>

    <script>
        window.history_cords = null;
        window.history_items = null;
        window.history_sensors = null;
        window.history_sensors_values = null;
    </script>
@endif