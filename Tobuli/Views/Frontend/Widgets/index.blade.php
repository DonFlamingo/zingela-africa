<?php
$widgets = Auth::user()->getSettings('widgets');
if ( empty($widgets) ) {
    $widgets = settings('widgets');
}
?>
@if( ! empty($widgets['status']) )
<div id="widgets">
    <a class="btn-collapse" onclick="app.changeSetting('toggleWidgets');"><i></i></a>

    <div class="widgets-content">
        @foreach( $widgets['list'] as $widget)
            @include('Frontend.Widgets.'.$widget)
        @endforeach
    </div>
</div>
@endif
