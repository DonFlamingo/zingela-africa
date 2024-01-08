<?php namespace Tobuli\Repositories\EventCustom;

use Tobuli\Entities\EventCustom as Entity;
use Tobuli\Repositories\EloquentRepository;
use Illuminate\Support\Facades\DB;

class EloquentEventCustomRepository extends EloquentRepository implements EventCustomRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
        $this->searchable = [
            'message',
        ];
    }

    public function getProtocols($user_id, $protocols = NULL) {
        $query = $this->entity
            ->distinct('protocol');

        if (!is_null($protocols)) {
            $query->whereIn('protocol', $protocols);
        }

        return $query->where('user_id', $user_id)
            ->orderBy('protocol', 'asc')
            ->get();
    }

    public function whereProtocols($ids, $protocols) {
        return $this->entity
            ->whereIn('id', $ids)
            ->whereIn('protocol', $protocols)
            ->get();
    }

    public function searchAndPaginate(array $data, $sort_by, $sort = 'asc', $limit = 10)
    {
		
        $data = $this->generateSearchData($data);
        $sort = array_merge([
            'sort' => $sort,
            'sort_by' => $sort_by
        ], $data['sorting']);
		
		$items = $this->entity
            ->with(['tags'])
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
}