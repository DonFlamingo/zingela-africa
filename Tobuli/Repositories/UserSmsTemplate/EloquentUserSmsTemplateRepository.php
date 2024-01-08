<?php namespace Tobuli\Repositories\UserSmsTemplate;

use Tobuli\Entities\UserSmsTemplate as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentUserSmsTemplateRepository extends EloquentRepository implements UserSmsTemplateRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }
}