<?php

namespace Facades\Repositories;

use Illuminate\Support\Facades\Facade;

class DeviceIconRepo extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Tobuli\Repositories\DeviceIcon\DeviceIconRepositoryInterface';
    }
}