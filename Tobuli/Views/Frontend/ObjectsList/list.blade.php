<table class="table table-list">
    <thead>
    <tr>
        @foreach($columns as $column)
        <th>{{ $column['title'] }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($grouped as $key => $devices)
        @foreach($devices as $device)
        <tr>
            @foreach($columns as $column)
            <td>
                @if($column['field'] == 'status')
                <span class="device-status" style="background-color: {{ $device['status_color'] }};" title="{{ trans('global.'.$device['status']) }}"></span>
                @elseif($column['field'] == 'position')
                    @if ($device['lat'] && $device['lng'])
                    <a href="http://maps.google.com/maps?q={{ $device['lat'] }},{{ $device['lng'] }}&t=m" target="_blank">
                    {{ $device['lat'] }}&deg;, {{ $device['lng'] }}&deg;
                    </a>
                    @endif
                @else
                    <?php $color = !empty($device['color'][$column['field']]) ? $device['color'][$column['field']] : 'inherit'; ?>
                    <span style="color: {{ $color }};">{{ $device[$column['field']] or '-' }}</span>
                @endif
            </td>
            @endforeach
        </tr>
        @endforeach
    @endforeach
    </tbody>
</table>