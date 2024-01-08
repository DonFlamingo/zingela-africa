@if (count($collection = $items->getCollection()))
    <div class="table-icon">
        @foreach ($collection as $item)
            <div class="item">
                <div class="controls">
                    <a href="javascript:" class="btn btn-default" data-id="{{ $item->id }}"><i class="icon delete"></i></a>
                </div>
                <img src="{{ asset($item->path) }}" alt="Image">
            </div>
        @endforeach
    </div>
@else
    {{ trans('admin.no_data') }}
@endif

@include("Admin.Layouts.partials.pagination")