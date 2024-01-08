<?php namespace Tobuli\Repositories\Config;

use Tobuli\Repositories\EloquentRepositoryInterface;

interface ConfigRepositoryInterface extends EloquentRepositoryInterface {
    public function whereTitle($title);
}