<?php namespace Tobuli\Repositories\SmsTemplate;

use Tobuli\Repositories\EloquentRepositoryInterface;

interface SmsTemplateRepositoryInterface extends EloquentRepositoryInterface {
    public function whereName($name);
}