<?php
function utf8ize($d)
{ 
    if (is_array($d) || is_object($d))
        foreach ($d as &$v) $v = utf8ize($v);
    else
        return utf8_encode($d);

    return $d;
}
?>

@if (!empty($events))
    @foreach ($events as $item)
        <tr data-event-id="{!!$item->id!!}" onClick="app.events.select({!!$item->id!!});">
            <td class="datetime">
                <span class="time">{!! \Carbon\Carbon::parse($item->time)->format('H:i:s') !!}</span>
                <span class="date">{!! \Carbon\Carbon::parse($item->time)->format('Y-m-d') !!}</span>
            </td>
            <td>{{(isset($item->device->name) ? $item->device->name : '') }}</td>
            <td>{!!$item->message!!}@if (isset($item->geofence)) ({{$item->geofence->name}}) @endif</td>
  			<script>app.events.add({!! json_encode(utf8ize($item->toArray())) !!});</script>
        </tr>
    @endforeach
    <div style="display: none;">
        @if (method_exists($events, 'render'))
            {!!$events->render()!!}
        @endif
    </div>
@else
    <tr>
        <td class="no-data">{!!trans('front.no_events')!!}</td>
    </tr>
@endif
