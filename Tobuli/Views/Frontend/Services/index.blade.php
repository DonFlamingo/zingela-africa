<div class="table-responsive">
    <table class="table table-list">
        <thead>
            <tr>
                {!! tableHeader('validation.attributes.name') !!}
                {!! tableHeader('validation.attributes.expiration_date') !!}
                <th></th>
            </tr>
        </thead>
        <tbody>
        @if (count($services))
            @foreach ($services as $service)
                <tr>
                    <td>
                        {{$service->name}}
                    </td>
                    <td>
                        {{$service->expires}}
                    </td>
                    <td class="actions">
                        <a href="javascript:" class="btn icon edit" data-url="{!!route('services.edit', $service->id)!!}" data-modal="services_edit"></a>
                        <a href="javascript:" class="btn icon delete" data-url="{!!route('services.do_destroy', $service->id)!!}" data-modal="services_destroy"></a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="3">{!!trans('front.no_services')!!}</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="nav-pagination">
    {!! $services->setPath(route('services.index', $device_id))->render() !!}
</div>