<?php namespace Tobuli\Repositories\DeviceExpense;

use Tobuli\Repositories\EloquentRepository;
use Tobuli\Entities\DeviceExpense as Entity;

class EloquentDeviceExpenseRepository extends EloquentRepository implements DeviceExpenseRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
        $this->searchable = [];
    }

    public function deleteDeviceWhereNotIn($arr, $device_id, $id = 'id') {
        return $this->entity->where('device_id', $device_id)->whereNotIn($id, $arr)->delete();
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
            ->with('devices')
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

    public function getBetween($user_id, $from, $to) {
        return $this->entity
            ->with('devices')
            ->whereBetween('date', [$from, $to])
            ->where(['user_id' => $user_id])
            ->orderBy('date', 'asc')
            ->get();
    }
}