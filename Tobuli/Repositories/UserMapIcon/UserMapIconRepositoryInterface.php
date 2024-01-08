<?php namespace Tobuli\Repositories\UserMapIcon;

use Tobuli\Repositories\EloquentRepositoryInterface;

interface UserMapIconRepositoryInterface extends EloquentRepositoryInterface {

    public function whereUserId($user_id);

    public function updateWhereIconIds($ids, $data);

}