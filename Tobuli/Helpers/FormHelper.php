<?php

function error_for($attribute, $errors) {
    echo $errors->first($attribute, '<span class="help-block error">:message</span>');
}