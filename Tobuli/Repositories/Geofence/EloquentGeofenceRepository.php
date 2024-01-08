<?php namespace Tobuli\Repositories\Geofence;

use Illuminate\Support\Facades\DB;
use mysqli;
use Tobuli\Entities\Geofence as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentGeofenceRepository extends EloquentRepository implements GeofenceRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }

    public function whereUserId($user_id)
    {
        return $this->entity->where('user_id', $user_id)->get();
    }

    public function create($data) {
        if (!is_array($data['polygon']))
            $data['polygon'] = json_decode($data['polygon'], TRUE);

        $polygon = [];
        foreach ($data['polygon'] as $poly) {
            array_push($polygon, ['lat' => floatval($poly['lat']), 'lng' => floatval($poly['lng'])]);
        }

        $coordinates = json_encode($polygon);

        $cor_text = gen_polygon_text($polygon);

        $item = $this->entity->create([
            'active' => (isset($data['active']) ? $data['active'] : 1),
            'user_id' => $data['user_id'],
            'name' => $data['name'],
            'polygon_color' => $data['polygon_color'],
            'group_id' => (!isset($data['group_id']) || $data['group_id'] == 0 ? NULL : $data['group_id'])
        ]);

        DB::unprepared("UPDATE geofences SET coordinates = '".$coordinates."', polygon = PolygonFromText('POLYGON(({$cor_text}))') WHERE id = '{$item->id}'");
        return $item;
    }

    public function updateWithPolygon($id, $data) {
        if (!is_array($data['polygon']))
            $data['polygon'] = json_decode($data['polygon'], TRUE);

        $polygon = [];
        foreach ($data['polygon'] as $poly) {
            array_push($polygon, ['lat' => floatval($poly['lat']), 'lng' => floatval($poly['lng'])]);
        }

        $coordinates = json_encode($polygon);

        $cor_text = gen_polygon_text($data['polygon']);
        $this->entity->where('id', $id)->update([
            'name' => $data['name'],
            'polygon_color' => $data['polygon_color']
        ] + (isset($data['group_id']) ? ['group_id' => ($data['group_id'] == 0 ? NULL : $data['group_id'])] : []));

        DB::unprepared("UPDATE geofences SET coordinates = '".$coordinates."', polygon = PolygonFromText('POLYGON(({$cor_text}))') WHERE id = '{$id}'");
    }
}