<?php namespace Tobuli\Repositories\MapIcon;

use Tobuli\Entities\MapIcon as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentMapIconRepository extends EloquentRepository implements MapIconRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }

    public function whereNotInFirst($ids)
    {
        return $this->entity->whereNotIn('id', $ids)->first();
    }
}