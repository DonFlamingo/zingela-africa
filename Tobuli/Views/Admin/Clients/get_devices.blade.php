<table class="table table-condensed">
    <tr class="">
        {!! tableHeader('validation.attributes.title') !!}
        {!! tableHeader('validation.attributes.imei') !!}
        {!! tableHeader('global.online', 'style="text-align:center"') !!}
        {!! tableHeader('validation.attributes.expiration_date') !!}
        {!! tableHeader('admin.actions') !!}
    </tr>
    <tbody>
@if (count($items))
    @foreach ($items as $device)
        <tr>
            <td>{{ $device->name }}</td>
            <td>{{ $device->imei }}</td>
            <td style="text-align: center">
                <?php $online = isDeviceOnline($device->traccar->server_time, $device->traccar->ack_time); ?>
                <span class="device-status" style="background-color: {{ $online == 'online' ? 'green' : ($online == 'offline' ? 'red' : 'yellow') }}"></span>
            </td>
            <td>
                {{ $device->expiration_date == '0000-00-00' ? trans('front.unlimited') : datetime($device->expiration_date) }}
            </td>
            <td>
                <div class="btn-group dropdown" data-position="fixed">
                    <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:" data-modal="clients_device_edit" data-url="{{ route("devices.edit", [$device->id, 1]) }}">{{ trans('global.edit') }}</a></li>
                        <li><a href="{{ route('objects.destroy') }}" class="js-confirm-link" data-confirm="{!! trans('front.do_object_delete') !!}" data-id="{{ $device->id }}" data-method="DELETE">{{ trans('global.delete') }}</a></li>
                    </ul>
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td class="no-data" colspan="5">
            {{ trans('admin.no_data') }}
        </td>
    </tr>
@endif
    </tbody>
</table>