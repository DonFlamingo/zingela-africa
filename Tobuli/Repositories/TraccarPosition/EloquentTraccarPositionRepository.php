<?php namespace Tobuli\Repositories\TraccarPosition;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class EloquentTraccarPositionRepository implements TraccarPositionRepositoryInterface {

    public function searchWithSensors($user_id, $device_id, $date_from, $date_to, $sort = 'asc')
    {
        $table_name = 'sensors_'.$device_id;
        if (!Schema::connection('sensors_mysql')->hasTable($table_name)) {
            Schema::connection('sensors_mysql')->create($table_name, function(Blueprint $table)
            {
                $table->bigIncrements('id');
                $table->bigInteger('position_id')->unsigned()->index();
                $table->text('other')->nullable();
                $table->datetime('time')->nullable()->index();
            });
        }
        $query = DB::connection('traccar_mysql')
            ->table('positions_'.$device_id)
            ->select('positions_'.$device_id.'.*', 'sensors.other as sensor_other', 'sensors.time as sensor_time', 'sensors.id as sensor_id')
            ->leftJoin('tracking_sensors.sensors_'.$device_id.' as sensors', 'positions_'.$device_id.'.id', '=', 'sensors.position_id')
            ->join('tracking_web.devices as devices', 'positions_'.$device_id.'.device_id', '=', 'devices.traccar_device_id')
            ->join('tracking_web.user_device_pivot as user_device', 'devices.id', '=', 'user_device.device_id')
            ->join('tracking_web.users as users', 'user_device.user_id', '=', 'users.id')
            ->where('users.id', $user_id)
            //->whereBetween('sensors.time', [$date_from, $date_to])
            ->whereRaw("(positions_".$device_id.".time BETWEEN '{$date_from}' AND '{$date_to}' OR sensors.time BETWEEN '{$date_from}' AND '{$date_to}')")
            ->groupBy('sensors.id', 'positions_'.$device_id.'.id')
            ->orderBy('positions_'.$device_id.'.time', $sort)
            ->orderBy('sensors.time', $sort);
        $result = $query->get();

        $result = json_decode(json_encode($result), TRUE);
        foreach ($result as &$item) {
            if (isset($item['sensor_time']) && !is_null($item['sensor_time']) && strtotime($item['sensor_time']) >= strtotime($item['time'])) {
                $item['time'] = $item['sensor_time'];
                $item['other'] = $item['sensor_other'];
            }
            unset($item['sensor_other'], $item['sensor_time']);
            $item['sort'] = strtotime($item['time']);
        }

        if ($sort == 'asc')
            usort($result, 'cmp');
        else
            usort($result, 'rcmp');

        return $result;
    }

    public function search($user_id, $data, $paginate = FALSE, $limit = 50, $zone = NULL, $sort = 'asc')
    {
        if (is_null($zone))
            $zone = Auth::User()->timezone_reverse;
        $date_from = tdate($data['from_date'].' '.$data['from_time'], $zone);
        $date_to = tdate($data['to_date'].' '.$data['to_time'], $zone);
        $query = DB::connection('traccar_mysql')
            ->table('positions_'.$data['device_id'])
            ->select('positions_'.$data['device_id'].'.*', 'sensors.other as sensor_other', 'sensors.time as sensor_time')
            ->leftJoin('tracking_sensors.sensors_'.$data['device_id'].' as sensors', 'positions_'.$data['device_id'].'.id', '=', 'sensors.position_id')
            ->join('tracking_web.devices as devices', 'positions_'.$data['device_id'].'.device_id', '=', 'devices.traccar_device_id')
            ->join('tracking_web.user_device_pivot as user_device', 'devices.id', '=', 'user_device.device_id')
            ->join('tracking_web.users as users', 'user_device.user_id', '=', 'users.id')
            ->where('users.id', $user_id)
            ->whereBetween('positions_'.$data['device_id'].'.time', [$date_from, $date_to])
            ->groupBy('sensors.id', 'positions_'.$data['device_id'].'.id')
            //->orderBy('sensors.time', $sort)
            ->orderBy('positions_'.$data['device_id'].'.time', $sort)
            ->orderBy('sensors.time', $sort);

        return ($paginate ? $query->paginate($limit) : $query->get());
    }

    public function sumDistance($device_id, $range)
    {
        return DB::connection('traccar_mysql')
            ->table('positions_'.$device_id)
            ->select(DB::raw('SUM(distance) as sum'))
            ->whereBetween(DB::raw('DATE(positions_'.$device_id.'.time)'), [$range[0], $range[1]])
            ->first();
    }

    public function sumDistanceHigher($device_id, $date_to)
    {
        return DB::connection('traccar_mysql')
            ->table('positions_'.$device_id)
            ->select(DB::raw('SUM(distance) as sum'))
            ->where('time', '>', $date_to)
            ->first();
    }

    public function getOldest($device_id) {
        return DB::connection('traccar_mysql')
            ->table("positions_{$device_id}")
            ->select(DB::raw('*, latitude as lastValidLatitude, longitude as lastValidLongitude'))
            ->orderBy('id', 'asc')
            ->first();
    }

    public function getNewer($device_id, $position_id = 0) {
        return DB::connection('traccar_mysql')
            ->table("positions_{$device_id}")
            ->select(DB::raw('*, latitude as lastValidLatitude, longitude as lastValidLongitude'))
            ->where('id', '>', $position_id)
            ->first();
    }

    public function getBetween($device_id, $from, $to) {
        return DB::connection('traccar_mysql')
            ->table("positions_{$device_id} as positions")
            ->leftJoin('tracking_sensors.sensors_'.$device_id.' as sensors', 'positions.id', '=', 'sensors.position_id')
            ->select(DB::raw("positions.*, sensors.time as sensor_time, sensors.other as sensor_other, DATE(positions.time) as date"))
            ->whereRaw("(positions.time BETWEEN '{$from}' AND '{$to}' OR sensors.time BETWEEN '{$from}' AND '{$to}')")
            //->whereBetween('positions.time', [$from, $to])
            //->groupBy('positions.id')
            ->orderBy('sensors.time', 'asc')
            ->get();
    }
}