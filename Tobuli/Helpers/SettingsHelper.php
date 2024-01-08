<?php

if (! function_exists('settings')) {
    /**
    * @param  array|string  $key
    * @param  array|string  $value
    * @return mixed
    */
    function settings($key = null, $value = null)
    {
        if (is_null($key)) {
            return app('Tobuli\Helpers\Settings');
        }

        if (is_null($value)) {
            return app('Tobuli\Helpers\Settings')->get($key);
        }

        return app('Tobuli\Helpers\Settings')->set($key, $value);
    }
}