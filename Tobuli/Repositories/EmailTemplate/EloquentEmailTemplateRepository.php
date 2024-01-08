<?php namespace Tobuli\Repositories\EmailTemplate;

use Tobuli\Entities\EmailTemplate as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentEmailTemplateRepository extends EloquentRepository implements EmailTemplateRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
        $this->searchable = [
            'title'
        ];
    }

    public function whereName($name)
    {
        return $this->entity->where('name', $name)->first();
    }
}