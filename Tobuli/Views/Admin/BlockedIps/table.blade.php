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
            {!! tableHeader('validation.attributes.ip') !!}
            {!! tableHeader('admin.actions', 'style="text-align: right;"') !!}
        </tr>
        </thead>
        <tbody>
            @foreach($files as $file)
                <tr>
                    <td>
                        {{ $file }}
                    </td>
                    <td class="actions">
                        <div class="btn-group dropdown droparrow" data-position="fixed">
                            <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                            <ul class="dropdown-menu">
                                <li><a href="javascript:" data-modal="blocked_ips_destroy" data-url="{{ route('admin.blocked_ips.do_destroy', [$file]) }}">{!! trans('global.delete') !!}</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach

            @if (empty($files))
                <tr>
                    <td colspan="2">
                        {{ trans('admin.no_data') }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>