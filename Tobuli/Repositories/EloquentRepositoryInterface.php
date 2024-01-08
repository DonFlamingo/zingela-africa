<?php namespace Tobuli\Repositories;

interface EloquentRepositoryInterface {
    public function all();

    public function find($id);

    public function findWhere($where);

    public function first($where);

    public function create($input);

    public function update($id, $input);

    public function updateWhere($where, $update);

    public function updateWhereIn($update, $arr, $id = 'id');

    public function delete($id);

    public function deleteWhere($where);

    public function deleteWhereIn($arr, $id = 'id');

    public function deleteWhereNotIn($arr, $id = 'id');

    public function getWhere(array $where, $sort = NULL, $sort_t = 'asc');

    public function getWhereSelect(array $where, array $select, $sort = NULL, $sort_t = 'asc');

    public function searchAndPaginate(array $data, $sort_by, $sort = 'asc', $limit = '10');

    public function count();

    public function countwhere($where);

    public function getWhereInWith($arr, $id = 'id', $with = []);

    public function getWhereInWhere($arr, $id = 'id', $where);

}