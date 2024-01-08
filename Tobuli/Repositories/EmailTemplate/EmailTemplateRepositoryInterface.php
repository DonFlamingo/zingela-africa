<?php namespace Tobuli\Repositories\EmailTemplate;

use Tobuli\Repositories\EloquentRepositoryInterface;

interface EmailTemplateRepositoryInterface extends EloquentRepositoryInterface {
    public function whereName($name);
}