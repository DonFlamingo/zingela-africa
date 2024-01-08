<?php namespace Tobuli\Repositories\DeviceIcon;

use Tobuli\Entities\DeviceIcon as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentDeviceIconRepository extends EloquentRepository implements DeviceIconRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }

    public function whereNotInFirst($ids)
    {
        return $this->entity->whereNotIn('id', $ids)->first();
    }

    public function all()
    {
        return $this->entity->orderBy('order', 'desc')->orderBy('id', 'asc')->get();
    }

}