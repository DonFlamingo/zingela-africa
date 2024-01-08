<?php

namespace Facades\Repositories;

use Illuminate\Support\Facades\Facade;

class DeviceExpensesRepo extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Tobuli\Repositories\DeviceExpense\DeviceExpenseRepositoryInterface';
    }
}