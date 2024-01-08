<?php namespace Tobuli\Repositories\Alert;

use Tobuli\Repositories\EloquentRepositoryInterface;

interface AlertRepositoryInterface extends EloquentRepositoryInterface {

    public function whereUserId($user_id);

    public function findWithAttributes($id);
}