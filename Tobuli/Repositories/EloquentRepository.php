<?php namespace Tobuli\Repositories;

abstract class EloquentRepository implements EloquentRepositoryInterface
{
    protected $entity;

    protected $searchable = [];

    public function all()
    {
        return $this->entity->all();
    }

    public function find($id)
    {
        return $this->entity->find($id);
    }

    public function findWhere($where)
    {
        return $this->entity->where($where)->first();
    }

    public function first($where)
    {
        return $this->entity->where($where)->first();
    }

    public function create($input)
    {
        return $this->entity->create($input);
    }

    public function update($id, $input)
    {
        $item = $this->entity->find($id);
        return $item->update($input);
    }

    public function updateWhere($where, $update)
    {
        return $this->entity->where($where)->update($update);
    }

    public function updateWhereIn($update, $arr, $id = 'id')
    {
        return $this->entity->whereIn($id, $arr)->update($update);
    }

    public function delete($id) {
        return $this->entity->find($id)->delete();
    }

    public function deleteWhere($where) {
        return $this->entity->where($where)->delete();
    }

    public function deleteWhereIn($arr, $id = 'id') {
        return $this->entity->whereIn($id, $arr)->delete();
    }

    public function deleteWhereNotIn($arr, $id = 'id') {
        return $this->entity->whereNotIn($id, $arr)->delete();
    }

    public function searchAndPaginate(array $data, $sort_by, $sort = 'asc', $limit = 10)
    {
        $data = $this->generateSearchData($data);
        $sort = array_merge([
            'sort' => $sort,
            'sort_by' => $sort_by
        ], $data['sorting']);

        $items = $this->entity
            ->orderBy($sort['sort_by'], $sort['sort'])
            ->where(function ($query) use ($data) {
                if (!empty($data['search_phrase'])) {
                    foreach ($this->searchable as $column) {
                        $query->orWhere($column, 'like', '%' . $data['search_phrase'] . '%');
                    }
                }

                if (count($data['filter'])) {
                    foreach ($data['filter'] as $key=>$value) {
                        $query->where($key, $value);
                    }
                }
            })
            ->paginate($limit);

        $items->sorting = $sort;

        return $items;
    }

    protected function generateSearchData($data)
    {
        return array_merge([
            'sorting' => [],
            'search_phrase' => '',
            'filter' => []
        ], $data);
    }

    public function  count() {
        return $this->entity->count();
    }

    public function countWhere($where) {
        return $this->entity->where($where)->count();
    }

    public function getWhere(array $where, $sort = NULL, $sort_t = 'asc') {
        $query = $this->entity->where($where);
        if (!is_null($sort))
            $query->orderBy($sort, $sort_t);

        return $query->get();
    }

    public function getWithWhere($with, $where = []) {
        return $this->entity->with($with)->where($where)->get();
    }

    public function getWhereSelect(array $where, array $select, $sort = NULL, $sort_t = 'asc') {
        $query = $this->entity->select($select)->where($where);
        if (!is_null($sort))
            $query->orderBy($sort, $sort_t);

        return $query->get();
    }

    public function getWithFirst($with, $where = []) {
        return $this->entity->with($with)->where($where)->first();
    }

    public function whereWith($where = [], $with = []) {
        return $this->entity->with($with)->where($where)->get();
    }

    public function getWhereIn($arr, $id = 'id') {
        return $this->entity->whereIn($id, $arr)->get();
    }

    public function getWhereInWith($arr, $id = 'id', $with = []) {
        return $this->entity->whereIn($id, $arr)->with($with)->get();
    }

    public function getWhereInWhere($arr, $id = 'id', $where) {
        return $this->entity->whereIn($id, $arr)->where($where)->get();
    }
}