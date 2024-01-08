<?php namespace Tobuli\Repositories\DeviceGroup;

use Tobuli\Entities\DeviceGroup as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentDeviceGroupRepository extends EloquentRepository implements DeviceGroupRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }

    public function deleteUsersWhereNotIn($arr, $user_id, $id = 'id') {
        return $this->entity->where('user_id', $user_id)->whereNotIn($id, $arr)->delete();
    }
}