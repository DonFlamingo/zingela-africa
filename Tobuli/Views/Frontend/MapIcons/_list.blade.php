@foreach($mapIcons->toArray() as $key=>$value)
    <label>{!! Form::radio('map_icon_id', $value['id'], null, ['data-width' => $value['width'], 'data-height' => $value['height']]) !!} <img src="{{ asset($value['path']) }}" alt="ICON"></label>
@endforeach