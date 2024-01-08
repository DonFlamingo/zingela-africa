<div class="table-responsive">
    <table class="table table-list" data-toggle="multiCheckbox">
        <thead>
        <tr>
            {!! tableHeaderCheckall(['delete_url' => trans('admin.delete_selected')]) !!}
            {!! tableHeader('validation.attributes.title') !!}
            {!! tableHeader('validation.attributes.sensors_count') !!}
            {!! tableHeader('admin.actions', 'style="text-align: right;"') !!}
        </tr>
        </thead>
        <tbody>
        @if (count($items))
            @foreach ($items as $item)
                <tr>
                    <td>
                        <div class="checkbox">
                            <input type="checkbox" class="checkboxes" value="{!! $item->id !!}">
                            <label></label>
                        </div>
                    </td>
                    <td>{{ $item->title }}</td>
                    <td>{{ $item->count }}</td>
                    <td class="actions">
                        <div class="btn-group dropdown droparrow" data-position="fixed">
                            <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                            <ul class="dropdown-menu">
                                <li><a href="javascript:" data-modal="sensor_groups_edit" data-url="{{ route('admin.sensor_groups.edit', [$item->id]) }}">{!! trans('global.rename') !!}</a></li>
                                <li><a href="javascript:" data-modal="sensor_groups_show" data-url="{{ route('admin.sensor_group_sensors.index', [$item->id, '0']) }}">{!! trans('global.edit') !!}</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="no-data" colspan="4">
                    {!! trans('admin.no_data') !!}
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>