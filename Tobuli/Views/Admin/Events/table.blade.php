<div class="table_error"></div>
<div class="table-responsive">
    <table class="table table-list" data-toggle="multiCheckbox">
        <thead>
        <tr>
            {!! tableHeaderCheckall(['delete_url' => trans('admin.delete_selected')]) !!}
            {!! tableHeaderSort($items->sorting, 'protocol', trans('validation.attributes.device_protocol')) !!}
            {!! tableHeaderSort($items->sorting, 'tag', trans('front.tags')) !!}
            {!! tableHeaderSort($items->sorting, 'message') !!}
            {!! tableHeaderSort($items->sorting, 'always', trans('admin.show_always')) !!}
            {!! tableHeader('admin.actions', 'style="text-align: right;"') !!}
        </tr>
        </thead>
        <tbody>
        @if (count($collection = $items->getCollection()))
            @foreach ($collection as $item)
                <tr>
                    <td>
                        <div class="checkbox">
                            <input type="checkbox" value="{!! $item->id !!}" {{ $item->trial ? 'disabled="disabled"' : '' }}>
                            <label></label>
                        </div>
                    </td>
                    <td>
                        {{ $item->protocol }}
                    </td>
                    <td>
                        {{ !empty($item->tags) ? implode(', ', array_fetch($item->tags->toArray(), 'tag')) : '' }}
                    </td>
                    <td>
                        {{ $item->message }}
                    </td>
                    <td>
                        <span class="label label-sm label-{{ $item->always ? 'success' : 'danger' }}">
                            {{ trans('admin.' . ($item->always ? 'yes' : 'no')) }}
                        </span>
                    </td>
                    <td class="actions">
                        <div class="btn-group dropdown droparrow" data-position="fixed">
                            <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                            <ul class="dropdown-menu">
                                <li><a href="javascript:" data-modal="{{ $section }}_edit" data-url="{{ route("admin.{$section}.edit", $item->id) }}">{{ trans('global.edit') }}</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr class="">
                <td class="no-data" colspan="6">
                    {!! trans('admin.no_data') !!}
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

@include("Admin.Layouts.partials.pagination")
