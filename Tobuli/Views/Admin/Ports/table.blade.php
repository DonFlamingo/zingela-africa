@if (Session::has('message'))
    <div class="alert alert-success">
        {{ Session::get('message') }}
    </div>
@endif
@if (Session::has('error'))
    <div class="alert alert-danger">
        {{ Session::get('error') }}
    </div>
@endif

<div class="table-responsive">
    <table class="table table-list" data-toggle="multiCheckbox">
        <thead>
        <tr>
            {!! tableHeader('validation.attributes.active') !!}
            {!! tableHeader('validation.attributes.port') !!}
            {!! tableHeader('validation.attributes.name') !!}
            {!! tableHeader('validation.attributes.extra') !!}
            {!! tableHeader('admin.actions', 'style="text-align: right;"') !!}
        </tr>
        </thead>
        <tbody>
        @if (count($ports))
            @foreach($ports as $port)
                <tr>
                    <td>
                        <span class="label label-sm label-{!! $port->active ? 'success' : 'danger' !!}">
                            {!! trans('validation.attributes.active') !!}
                        </span>
                    </td>
                    <td>{{ $port->port }}</td>
                    <td>{{ $port->name }}</td>
                    <td>{{ count(json_decode($port->extra, TRUE)) }}</td>
                    <td class="actions">
                        <div class="btn-group dropdown droparrow" data-position="fixed">
                            <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                            <ul class="dropdown-menu">
                                <li><a href="javascript:" data-modal="ports_edit" data-url="{{ route('admin.ports.edit', $port->name) }}">{!! trans('global.edit') !!}</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="5">
                    {{ trans('admin.no_data') }}
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>