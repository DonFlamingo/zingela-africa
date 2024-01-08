<div class="table-responsive">
    <table class="table table-list">
        <thead>
        {!! tableHeader('validation.attributes.name') !!}
        {!! tableHeader('validation.attributes.device_id') !!}
        {!! tableHeader('validation.attributes.rfid') !!}
        {!! tableHeader('validation.attributes.phone') !!}
        {!! tableHeader('validation.attributes.email') !!}
        {!! tableHeader('validation.attributes.description') !!}
        <th></th>
        </thead>
        <tbody>
        @if (count($drivers))
            @foreach ($drivers as $driver)
                <tr>
                    <td>{{$driver->name}}</td>
                    <td>{{empty($driver->device) ? '' : $driver->device->name}}</td>
                    <td>{{$driver->rfid}}</td>
                    <td>{{$driver->phone}}</td>
                    <td>{{$driver->email}}</td>
                    <td>{{$driver->description}}</td>
                    <td class="actions">
                        <a href="javascript:" class="btn icon edit" data-url="{!!route('user_drivers.edit', $driver->id)!!}" data-modal="user_drivers_edit"></a>
                        <a href="javascript:" class="btn icon delete" data-url="{!!route('user_drivers.do_destroy', $driver->id)!!}" data-modal="user_drivers_destroy"></a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="no-data" colspan="8">{!!trans('front.no_drivers')!!}</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="nav-pagination">
    {!! $drivers->setPath(route('user_drivers.index'))->render() !!}
</div>