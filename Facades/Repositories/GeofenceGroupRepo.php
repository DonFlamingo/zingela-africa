<?php

namespace Facades\Repositories;

use Illuminate\Support\Facades\Facade;

class GeofenceGroupRepo extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Tobuli\Repositories\GeofenceGroup\GeofenceGroupRepositoryInterface';
    }
}