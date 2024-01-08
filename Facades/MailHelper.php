<?php

namespace Facades;

use Illuminate\Support\Facades\Facade;

class MailHelper extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'App\Services\Mail\MailHelper';
    }
}