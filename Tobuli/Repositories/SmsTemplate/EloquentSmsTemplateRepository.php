<?php namespace Tobuli\Repositories\SmsTemplate;

use Tobuli\Entities\SmsTemplate as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentSmsTemplateRepository extends EloquentRepository implements SmsTemplateRepositoryInterface {

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