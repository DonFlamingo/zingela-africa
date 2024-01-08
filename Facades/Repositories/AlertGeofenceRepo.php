<?php

namespace Facades\Repositories;

use Illuminate\Support\Facades\Facade;

class AlertGeofenceRepo extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Tobuli\Repositories\AlertGeofence\AlertGeofenceRepositoryInterface';
    }
}