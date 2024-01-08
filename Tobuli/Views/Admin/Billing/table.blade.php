<div class="table-responsive">
    <table class="table table-list" data-toggle="multiCheckbox">
        <thead>
        <tr>
            {!! tableHeaderCheckall() !!}
            {!! tableHeader('validation.attributes.title') !!}
            {!! tableHeader('validation.attributes.price') !!}
            {!! tableHeader('validation.attributes.objects') !!}
            {!! tableHeader('validation.attributes.duration_value') !!}
            {!! tableHeader('admin.actions', 'style="text-align: right"') !!}
        </tr>
        </thead>
        <tbody>
        @if (count($items))
            @foreach ($items as $item)
                <tr>
                    <td>
                        <div class="checkbox">
                            <input type="checkbox" class="checkboxes" value="{!! $item->id !!}" {{ $item->id == settings('main_settings.default_billing_plan') ? 'disabled="disabled"' : '' }}>
                            <label></label>
                        </div>
                    </td>
                    <td>{{ $item->title }}</td>
                    <td>{{ $item->price }}</td>
                    <td>{{ $item->objects }}</td>
                    <td>{{ $item->duration_value }} {{ trans('front.'.$item->duration_type) }}</td>
                    <td class="actions">
                        <div class="btn-group dropdown droparrow" data-position="fixed">
                            <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                            <ul class="dropdown-menu">
                                <li><a href="javascript:" data-modal="billing_plans_edit" data-url="{{ route('admin.billing.edit', $item->id) }}">{!! trans('global.edit') !!}</a></li>
        						<li><a href="javascript:" data-modal="billing_plans_edit" data-url="{{ route('admin.billing.billing_plans_getDestroyOne', $item->id) }}">{!! trans('global.delete') !!}</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="no-data" colspan="12">
                    {!! trans('admin.no_data') !!}
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>