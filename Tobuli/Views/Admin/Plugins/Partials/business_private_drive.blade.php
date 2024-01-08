<div class="form-inline">
    <div class="form-group">
        {!! Form::label('business_drive_color', trans('front.drive_business').' '.trans('validation.attributes.color')) !!}
        {!!Form::text('plugins['.$plugin->key.'][options][business_color][value]', $plugin->options['business_color']['value'], ['class' => 'form-control colorpicker'])!!}
    </div>
    <br>
    <div class="form-group">
        {!! Form::label('private_drive_color', trans('front.drive_private').' '.trans('validation.attributes.color')) !!}
        {!!Form::text('plugins['.$plugin->key.'][options][private_color][value]', $plugin->options['private_color']['value'], ['class' => 'form-control colorpicker'])!!}
    </div>
</div>