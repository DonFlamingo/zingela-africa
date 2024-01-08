<div class="table-responsive">
    <table class="table table-list">
        <thead>
            <tr>
                {!! tableHeader('validation.attributes.sensor_name') !!}
                {!! tableHeader('validation.attributes.sensor_template') !!}
                <th></th>
            </tr>
        </thead>
        <tbody>
        @if (count($sensors))
            @foreach ($sensors as $sensor)
                <tr>
                    <td>
                        {{$sensor->name}}
                    </td>
                    <td>
                        {{$sensor->type_title}}
                    </td>
                    <td class="actions">
                        <a href="javascript:" class="btn icon edit" data-url="{!!route('sensors.edit', $sensor->id)!!}" data-modal="sensors_edit"></a>
                        <a href="javascript:" class="btn icon delete" data-url="{!!route('sensors.do_destroy', $sensor->id)!!}" data-modal="sensors_destroy"></a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="no-data" colspan="3">{!!trans('front.no_sensors')!!}</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="nav-pagination">
    {!! $sensors->setPath(route('sensors.index', $device_id))->render() !!}
</div>