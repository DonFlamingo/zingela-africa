{{-- TODO confirm link --}}
<div class="table_error"></div>
<div class="table-responsive">
    <table class="table table-list" data-toggle="multiCheckbox">
        <thead>
        <tr>
            {!! tableHeaderCheckall(['delete_url' => trans('admin.delete_selected')]) !!}
            {!! tableHeaderSort($items->sorting, 'devices.name', 'validation.attributes.name') !!}
            {!! tableHeaderSort($items->sorting, 'devices.imei', 'validation.attributes.imei') !!}
            {!! tableHeader('global.online', 'style="text-align:center;"') !!}
            {!! tableHeaderSort($items->sorting, 'traccar.time', 'admin.last_connection') !!}
            {!! tableHeaderSort($items->sorting, 'expiration_date', 'validation.attributes.expiration_date') !!}
            {!! tableHeader('validation.attributes.user') !!}
            {!! tableHeader('admin.actions', 'style="text-align: right;"') !!}
        </tr>
        </thead>

        <tbody>
        @if (count($collection = $items->getCollection()))
            @foreach ($collection as $item)
                <tr>
                    <td>
                        <div class="checkbox">
                            <input type="checkbox" value="{!! $item->id !!}">
                            <label></label>
                        </div>
                    </td>
                    <td>
                        {{ $item->name }}
                    </td>
                    <td>
                        {{ $item->imei }}
                    </td>
                    <td style="text-align: center">
                        <?php $online = isDeviceOnline($item->traccar->server_time, $item->traccar->ack_time); ?>
                        <span class="device-status" style="background-color: {{ $online == 'online' ? 'green' : ($online == 'offline' ? 'red' : 'yellow') }}"></span>
                    </td>
                    <td>
                        {{ $item->time }}
                    </td>
                    <td>
                        {{ $item->expiration_date == '0000-00-00' ? trans('front.unlimited') : datetime($item->expiration_date) }}
                    </td>
                    <td class="user-list">
                        {{ parseUsers($item->users->lists('email', 'id')) }}
                    </td>
                    <td class="actions">
                        <div class="btn-group dropdown droparrow" data-position="fixed">
                            <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                            <ul class="dropdown-menu">
                                <li><a href="javascript:" data-modal="{{ $section }}_edit" data-url="{{ route("devices.edit", [$item->id, 1]) }}">{{ trans('global.edit') }}</a></li>
                                <li><a href="{{ route('objects.destroy') }}" class="js-confirm-link" data-confirm="{!! trans('front.do_object_delete') !!}" data-id="{{ $item->id }}" data-method="DELETE">{{ trans('global.delete') }}</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr class="">
                <td class="no-data" colspan="7">
                    {!! trans('admin.no_data') !!}
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

@include("Admin.Layouts.partials.pagination")