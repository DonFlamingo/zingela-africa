<?php namespace Tobuli\Repositories\MapIcon;

use Tobuli\Repositories\EloquentRepositoryInterface;

interface MapIconRepositoryInterface extends EloquentRepositoryInterface {
    public function whereNotInFirst($ids);
}