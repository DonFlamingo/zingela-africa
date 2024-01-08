<?php namespace Tobuli\Repositories\Config;

use Tobuli\Entities\Config as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentConfigRepository extends EloquentRepository implements ConfigRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }

    public function whereTitle($title) {
        return Entity::where('title', $title)->first();
    }
}