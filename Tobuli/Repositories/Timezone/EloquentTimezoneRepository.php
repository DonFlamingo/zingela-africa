<?php namespace Tobuli\Repositories\Timezone;

use Illuminate\Support\Facades\Cache;
use Tobuli\Repositories\EloquentRepository;
use Tobuli\Entities\Timezone as Entity;

class EloquentTimezoneRepository extends EloquentRepository implements TimezoneRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
        $this->searchable = [];
    }

    public function order() {
        return $this->entity->orderBy('order')->get();
    }

    public function getList() {
        $timezones = Cache::get('timezones');

        if ( ! $timezones ) {
            $timezones = $this->entity->orderBy('order')->get()->lists('zone', 'id')->all();

            Cache::put('timezones', $timezones, 1);
        }

        return $timezones;
    }
}