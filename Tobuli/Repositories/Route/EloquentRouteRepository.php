<?php namespace Tobuli\Repositories\Route;

use Illuminate\Support\Facades\DB;
use Tobuli\Entities\Route as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentRouteRepository extends EloquentRepository implements RouteRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }

    public function whereUserId($user_id)
    {
        return $this->entity->where('user_id', $user_id)->get();
    }

    public function create($data) {
        $cor_text = $this->gen_polyline_text($data['polyline']);
        //dd('LINESTRING('.$cor_text.')');

        DB::unprepared("INSERT INTO routes (user_id, name, coordinates, polyline, color)
            VALUES('".$data['user_id']."', '".$data['name']."', '".$data['polyline']."',
            GeomFromText('LINESTRING($cor_text)'), '".$data['color']."'
        )");
    }

    public function updateWithPolyline($id, $data) {
        $cor_text = $this->gen_polyline_text($data['polyline']);

        DB::unprepared("UPDATE routes SET name = '".$data['name']."', coordinates = '".$data['polyline']."', polyline = GeomFromText('LINESTRING($cor_text)'), color = '".$data['color']."' WHERE id = '{$id}'");
    }

    private function gen_polyline_text($json) {
        $cor_text = NULL;
        $items = json_decode($json);
        foreach($items as $item) {
            $cor_text .= $item->lat.' '.$item->lng.',';
        }
        $cor_text = substr($cor_text, 0, -1);
        return $cor_text;
    }
}