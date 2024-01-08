{!! Form::hidden('trans_file', $file) !!}
@if ($file != 'validation')
    @foreach($en_translations as $key => $value)
        @if (empty($value))
            <?php continue; ?>
        @endif
        <tr>
            <td style="min-width: 30%; overflow: hidden">{!! $value !!}</td>
            <td style="min-width: 30%; overflow: hidden">{!! !array_key_exists($key, $or_translations) ? $value : $or_translations[$key] !!}</td>
            <td style="min-width: 40%"><textarea name="trans[{{ $key }}]" data-key="{{ $key }}" class="form-control" rows="3">{{ !array_key_exists($key, $translations) ? $value : $translations[$key] }}</textarea></td>
        </tr>
    @endforeach
@else
    @foreach($en_translations as $key => $value)
        @if (empty($value))
            <?php continue; ?>
        @endif

        @if (is_array($value))
            @foreach($value as $skey => $svalue)
                @if (empty($svalue) || is_array($svalue))
                    <?php continue; ?>
                @endif
                <tr>
                    <td style="min-width: 30%; overflow: hidden">{!! $svalue !!}</td>
                    <td style="min-width: 30%; overflow: hidden">{!! !array_key_exists($skey, $or_translations[$key]) ? $svalue : $or_translations[$key][$skey] !!}</td>
                    <td style="min-width: 40%"><textarea name="trans[{{ $key }}][{{ $skey }}]" data-key="{{ $key.'.'.$skey }}" class="form-control" rows="3">{{ !array_key_exists($skey, $translations[$key]) ? $svalue : $translations[$key][$skey] }}</textarea></td>
                </tr>
            @endforeach
        @else
        <tr>
            <td style="min-width: 30%; overflow: hidden">{!! $value !!}</td>
            <td style="min-width: 30%; overflow: hidden">{!! !array_key_exists($key, $or_translations) ? $value : $or_translations[$key] !!}</td>
            <td style="min-width: 40%"><textarea name="trans[{{ $key }}]" data-key="{{ $key }}" class="form-control" rows="3">{{ !array_key_exists($key, $translations) ? $value : $translations[$key] }}</textarea></td>
        </tr>
        @endif
    @endforeach
@endif
