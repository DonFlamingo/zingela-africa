<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover dataTable">
        <thead>
        <tr role="row">
            {!! tableHeaderCheckall() !!}
            {!! tableHeaderSort($items->sorting, 'trial') !!}
            {!! tableHeaderSort($items->sorting, 'name') !!}
            {!! tableHeaderSort($items->sorting, 'period_name') !!}
            {!! tableHeaderSort($items->sorting, 'price') !!}
            {!! tableHeaderSort($items->sorting, 'devices_limit') !!}
            {!! tableHeaderSort($items->sorting, 'days') !!}
            {!! tableHeader('admin.actions') !!}
        </tr>
        </thead>

        <tbody role="alert" aria-live="polite" aria-relevant="all">
        @if (count($collection = $items->getCollection()))
            @foreach ($collection as $item)
                <tr>
                    <td>
                        <input type="checkbox" class="checkboxes" value="{{ $item->id }}" {{ $item->trial ? 'disabled="disabled"' : '' }}>
                    </td>
                    <td>
                        <span class="label label-sm label-{{ $item->trial ? 'success' : 'danger' }}">
                            {{ trans('validation.attributes.trial') }}
                        </span>
                    </td>
                    <td>
                        {{ $item->name }}
                    </td>
                    <td>
                        {{ $item->period_name }}
                    </td>
                    <td>
                        {{ $item->price }}
                    </td>
                    <td>
                        {{ $item->devices_limit }}
                    </td>
                    <td>
                        {{ $item->days }}
                    </td>
                    <td>
                        <a href="javascript:" class="btn yellow btn-xs modal_open" data-modal="{{ $section }}_edit" data-url="{{ route("admin.{$section}.edit", $item->id) }}">{{ trans('global.edit') }}</a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr class="odd gradeX">
                <td colspan="12">
                    {{ trans('admin.no_data') }}
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="row">
    <div class="col-md-5 col-sm-12">

    </div>
    <div class="col-md-7 col-sm-12">
        <div class="dataTables_paginate paging_bootstrap">
            {!! $items->render() !!}
        </div>
    </div>
</div>