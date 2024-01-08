<?php namespace Tobuli\Repositories\Event;

use Tobuli\Entities\Event as Entity;
use Tobuli\Repositories\EloquentRepository;
use Illuminate\Support\Facades\Auth;

class EloquentEventRepository extends EloquentRepository implements EventRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }

    public function whereUserId($user_id) {
        return Entity::where('user_id', $user_id)->get();
    }

    public function whereUserIdWithAttributes($user_id, $search) {
        $queryy = Entity::with('device', 'geofence')
            ->select('events.*')
            ->addSelect('devices.name AS device_name')
            ->orderBy('id', 'desc')
            ->leftJoin('devices', 'events.device_id', '=', 'devices.id')
            ->where('events.user_id', $user_id)
            ->where('events.deleted', 0);

        if (empty($search)) {
            $events = $queryy->paginate(30);
        }
        else {
            $events = $queryy->where(function ($query) use ($search) {
                $query->where('events.message', 'like', '%' . $search . '%');
                $query->orWhere('devices.name', 'like', '%' . $search . '%');
            })
            ->limit(50)->get();
        }

        return $events;
    }

    public function findWithAttributes($id) {
        return Entity::where('id', $id)->with('device', 'alert')->first();
    }

    public function getHigherTime($user_id, $time) {
        return Entity::where(function($query) use($user_id, $time) {
            $query->where('user_id', $user_id);
            $query->where('created_at', '>', date('Y-m-d H:i:s', $time));
            return $query;
        })
            ->with('geofence', 'device', 'alert')->get();

    }

    public function search($data, $zone = NULL) {
        if (is_null($zone))
            $zone = Auth::User()->timezone_reverse;

        $date_from = tdate($data['from_date'].' '.$data['from_time'], $zone);
        $date_to = tdate($data['to_date'].' '.$data['to_time'], $zone);
        return Entity::orderBy('time', 'asc')
            ->whereBetween('time', [$date_from, $date_to])
            ->where('device_id', $data['device_id'])
            ->where('user_id', $data['user_id'])
            ->get()->toArray();
    }

    public function getBetween($user_id, $device_id, $from, $to) {
        return $this->entity
            ->with(['geofence'])
            ->whereBetween('time', [$from, $to])
            ->where([
                'user_id' => $user_id,
                'device_id' => $device_id
            ])
            ->orderBy('time', 'asc')
            ->get();
    }
}