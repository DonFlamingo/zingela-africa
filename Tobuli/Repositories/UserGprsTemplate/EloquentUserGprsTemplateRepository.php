<?php namespace Tobuli\Repositories\UserGprsTemplate;

use Tobuli\Entities\UserGprsTemplate as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentUserGprsTemplateRepository extends EloquentRepository implements UserGprsTemplateRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }
}