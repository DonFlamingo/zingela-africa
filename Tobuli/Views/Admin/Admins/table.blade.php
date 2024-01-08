<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover dataTable">
        <thead>
        <tr role="row">
            {!! tableHeaderCheckall() !!}
            {!! tableHeaderSort($items->sorting, 'active', NULL, 'style="width: 1%;"') !!}
            {!! tableHeaderSort($items->sorting, 'email') !!}
            {!! tableHeader('admin.actions') !!}
        </tr>
        </thead>

        <tbody role="alert" aria-live="polite" aria-relevant="all">
        @if (count($collection = $items->getCollection()))
            @foreach ($collection as $item)
                <tr>
                    <td>
                        <input type="checkbox" class="checkboxes" value="{{ $item->id }}">
                    </td>
                    <td>
                        <span class="label label-sm label-{{ $item->active ? 'success' : 'danger' }}">
                            {{ trans('validation.attributes.active') }}
						</span>
                    </td>
                    <td>
                        {{ $item->email }}
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