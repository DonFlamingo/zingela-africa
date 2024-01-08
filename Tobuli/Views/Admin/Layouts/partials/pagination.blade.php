<div class="nav-pagination">
    {!! $items->render() !!}
</div>
{{--
<div class="pagination">
    <a href="{{ $url_path }}?page={{ $page - 1 }}" class="pag @if ($page <= 1)disabled @endif" data-page="{{ $page - 1 }}">&#171;</a>
    @foreach($pagination as $key => $lpage)
        @if ($lpage == '.')
            <a href="#" class="pag_space"></a>
        @else
            <a href="{{ $url_path }}?page={{ $lpage }}" class="pag @if ($lpage == $page) active @endif" data-page="{{ $lpage }}">{{ $lpage }}</a>
        @endif
    @endforeach
    <a href="{{ $url_path }}?page={{ $page + 1 }}" class="pag @if ($page >= $total_pages)disabled @endif" data-page="{{ $page + 1 }}">&#187;</a>
</div>
--}}