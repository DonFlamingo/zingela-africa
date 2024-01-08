<?php

namespace Facades\Validators;

use Illuminate\Support\Facades\Facade;

class DeviceFormValidator extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Tobuli\Validation\DeviceFormValidator';
    }
}