<?php namespace ModalHelpers;

use Facades\Repositories\GeofenceGroupRepo;
use Facades\Repositories\GeofenceRepo;
use Facades\Validators\GeofenceFormValidator;
use Tobuli\Exceptions\ValidationException;
use Illuminate\Support\Facades\DB;
use Validator;
use Response;

class GeofenceModalHelper extends ModalHelper
{
    public function get()
    {
        $geofences = GeofenceRepo::whereUserId($this->user->id);
        if ($this->api) {
            $geofences = $geofences->toArray();
            foreach ($geofences as &$geofence)
                unset($geofence['polygon']);

            if (!$this->user->perm('geofences', 'view'))
                $geofences = [];

            return compact('geofences');
        }

        $geofence_groups = ['0' => trans('front.ungrouped')] + GeofenceGroupRepo::getWhere(['user_id' => $this->user->id])->lists('title', 'id')->all();
        $geofence_groups_opened = array_flip(json_decode($this->user->open_geofence_groups, TRUE));

        $grouped = [];
        foreach ($geofences as $geofence) {
            $group_id = empty($geofence->group_id) ? 0 : $geofence->group_id;
            $grouped[$group_id][] = $geofence;
        }
        unset($geofences);

        if (!$this->api && !$this->user->perm('geofences', 'view'))
            $grouped = [];

        return compact('grouped', 'geofence_groups', 'geofence_groups_opened');
    }

    public function create()
    {
        try
        {
            if (!$this->api && !$this->user->perm('geofences', 'edit'))
                throw new ValidationException(['_token' => trans('front.dont_have_permission')]);

            $this->validate('create');

           $geo =  GeofenceRepo::create($this->data + ['user_id' => $this->user->id]);

            return ['status' => 1 , 'geofence' => ['id'=> $geo->id ]];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    public function edit()
    {
        $item = GeofenceRepo::find($this->data['id']);

        try
        {
            if (!$this->api && !$this->user->perm('geofences', 'edit'))
                throw new ValidationException(['_token' => trans('front.dont_have_permission')]);
            
            if (empty($item) || $item->user_id != $this->user->id)
                throw new ValidationException(['polygon' => dontExist('front.geofence')]);

            $this->validate('update');

            GeofenceRepo::updateWithPolygon($item->id, $this->data);

            $devices = DB::table('geofences')
                ->select('devices.imei')
                ->join('alert_geofence', 'geofences.id', '=', 'alert_geofence.geofence_id')
                ->join('alert_device', 'alert_geofence.alert_id', '=', 'alert_device.alert_id')
                ->join('devices', 'alert_device.device_id', '=', 'devices.id')
                ->where('geofences.id', '=', $item->id)
                ->groupBy('devices.id')
                ->get();

            if (!empty($devices)) {
                foreach ($devices as $device)
                    clearCache($device->imei, 'alerts');
            }
            return ['status' => 1];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    private function validate($type)
    {
        GeofenceFormValidator::validate($type, $this->data);
    }

    public function changeActive()
    {
        if (!$this->api && !$this->user->perm('geofences', 'edit'))
            return ['status' => 1];
        
        $items = [];
        if (is_array($this->data['id']))
            $items = GeofenceRepo::getWhereIn($this->data['id']);
        else
            $items[] = GeofenceRepo::find($this->data['id']);

        $update_items = [];
        foreach ($items as $key => $item) {
            if ($item->user_id == $this->user->id || isAdmin())
                $update_items[$item->id] = $item->id;
        }

        if (!empty($update_items)) {
            DB::table('geofences')->where([
                'user_id' => $this->user->id
            ])->whereIn('id', $update_items)->update(['active' => (isset($this->data['active']) && $this->data['active'] != 'false') ? 1 : 0]);
        }

        return ['status' => 1];
    }

    public function import($content = NULL)
    {
        if (is_null($content))
            $content = $this->data['content'];

        libxml_use_internal_errors(true);

        $arr = @json_decode($content, TRUE);
        $xml = simplexml_load_string($content);
        if (!is_array($arr) && !$xml)
            return ['status' => 0, 'error' => trans('front.unsupported_format')];

        $groups_nr = 0;
        $geofences_nr = 0;
        $geofences_exists_nr = 0;

        try {
            // Default atrams goefences format
            if (is_array($arr)) {
                $groups = [];
                if (!empty($arr['groups'])) {
                    foreach ($arr['groups'] as $group) {
                        if ($group['id'] == 0)
                            continue;

                        $item = GeofenceGroupRepo::create([
                            'user_id' => $this->user->id,
                            'title' => $group['title']
                        ]);

                        $groups[$group['id']] = $item->id;
                        $groups_nr++;
                    }
                }
                if (!empty($arr['geofences'])) {
                    foreach ($arr['geofences'] as $geofence) {
                        $group_id = null;
                        if (isset($groups[$geofence['group_id']]))
                            $group_id = $groups[$geofence['group_id']];

                        $polygon = json_decode($geofence['coordinates'], TRUE);

                        $item = GeofenceRepo::findWhere(['coordinates' => json_encode($polygon), 'user_id' => $this->user->id]);
                        if (empty($item)) {
                            $geofences_nr++;
                            GeofenceRepo::create([
                                'user_id' => $this->user->id,
                                'group_id' => $group_id,
                                'name' => $geofence['name'],
                                'polygon' => $polygon,
                                'polygon_color' => $geofence['polygon_color']
                            ]);
                        }
                        else
                            $geofences_exists_nr++;
                    }
                }
            }

            // KML
            if ($xml) {
                $color = '#d000df';

                foreach ( $xml->Document->xpath('//Style') as $style) {
                    $color = '#'.$style->PolyStyle->color;
                }


                $folders = $xml->xpath("//*[name()='Folder']");

                if ( $folders ) {
                    foreach ( $folders as $folder ) {

                        $geoGroup = GeofenceGroupRepo::findWhere(['title' => $folder->name, 'user_id' => $this->user->id]);

                        if (empty($geoGroup)) {
                            $groups_nr++;
                            $geoGroup = GeofenceGroupRepo::create([
                                'user_id' => $this->user->id,
                                'title' => $folder->name,
                            ]);
                        }

                        foreach ( $folder->Placemark as $mark) {

                            $mark = json_decode(json_encode($mark), true);

                            $group_id = $geoGroup->id;
                            $polygon = [];
                            $coordinates = explode(" ", $mark['Polygon']['outerBoundaryIs']['LinearRing']['coordinates']);
                            if (empty($coordinates))
                                continue;
                            foreach ($coordinates as $cord) {
                                if (empty($cord))
                                    continue;

                                list($lng, $lat) = explode(',', $cord);
                                array_push($polygon, ['lat' => $lat, 'lng' => $lng]);
                            }
                            array_pop($polygon);

                            $item = GeofenceRepo::findWhere(['coordinates' => json_encode($polygon), 'user_id' => $this->user->id]);
                            if (empty($item)) {
                                $geofences_nr++;
                                GeofenceRepo::create([
                                    'active' => 0,
                                    'user_id' => $this->user->id,
                                    'group_id' => $group_id,
                                    'name' => $mark['name'],
                                    'polygon' => $polygon,
                                    'polygon_color' => $color
                                ]);
                            }
                            else
                                $geofences_exists_nr++;
                        }
                    }
                } else {

                    foreach ( $xml->xpath("//*[name()='Placemark']") as $mark) {
                        $mark = json_decode(json_encode($mark), true);

                        $group_id = null;
                        $polygon = [];
                        $coordinates = explode(" ", $mark['Polygon']['outerBoundaryIs']['LinearRing']['coordinates']);
                        if (empty($coordinates))
                            continue;
                        foreach ($coordinates as $cord) {
                            if (empty($cord))
                                continue;

                            list($lng, $lat) = explode(',', $cord);
                            array_push($polygon, ['lat' => $lat, 'lng' => $lng]);
                        }
                        array_pop($polygon);

                        $item = GeofenceRepo::findWhere(['coordinates' => json_encode($polygon), 'user_id' => $this->user->id]);
                        if (empty($item)) {
                            $geofences_nr++;
                            GeofenceRepo::create([
                                'active' => 0,
                                'user_id' => $this->user->id,
                                'group_id' => $group_id,
                                'name' => $mark['name'],
                                'polygon' => $polygon,
                                'polygon_color' => $color
                            ]);
                        }
                        else
                            $geofences_exists_nr++;
                    }
                }
            }
        }
        catch (\Exception $e) {
            return ['status' => 0, 'error' => trans('front.unsupported_format')];
        }

        return array_merge(['status' => 1, 'message' => strtr(trans('front.imported_geofences'), [':groups' => $groups_nr, ':geofences' => $geofences_nr])]);
    }

    public function export()
    {
        $geofences = [];
        $groups = [];
        if (isset($this->data['groups']) && is_array($this->data['groups'])) {
            $fl_groups = array_flip($this->data['groups']);
            $groups = GeofenceGroupRepo::getWhereInWhere($this->data['groups'], 'id', ['user_id' => $this->user->id])->toArray();
            if (isset($fl_groups['0']))
                $groups[]['id'] = NULL;
            foreach ($groups as &$group) {
                if (isset($group['user_id']))
                    unset($group['user_id']);

                $items = GeofenceRepo::getWhere(['group_id' => $group['id'], 'user_id' => $this->user->id])->toArray();
                foreach ($items as $geofence) {
                    unset($geofence['polygon'], $geofence['user_id'], $geofence['active'], $geofence['created_at'], $geofence['updated_at']);
                    array_push($geofences, $geofence);
                }
            }
        }

        if (isset($this->data['geofences']) && is_array($this->data['geofences'])) {
            $items = GeofenceRepo::getWhereInWhere($this->data['geofences'], 'id', ['user_id' => $this->user->id])->toArray();
            foreach ($items as $geofence) {
                unset($geofence['polygon'], $geofence['user_id'], $geofence['active'], $geofence['created_at'], $geofence['updated_at']);
                $geofence['coordinates'] = json_encode(json_decode($geofence['coordinates'], TRUE));
                array_push($geofences, $geofence);
            }
        }

        return compact('groups', 'geofences');
    }

    public function exportData()
    {
        $export_types = [
            'export_single' => trans('front.export_single'),
            'export_groups' => trans('front.export_groups'),
            'export_active' => trans('front.export_active'),
            'export_inactive' => trans('front.export_inactive')
        ];

        $geofences = GeofenceRepo::getWhere(['user_id' => $this->user->id])->lists('name', 'id')->all();

        return compact('export_types', 'geofences');
    }

    public function exportType()
    {
        $type = $this->data['type'];
        $selected = null;

        $items = GeofenceRepo::getWhere(['user_id' => $this->user->id])->lists('name', 'id')->all();

        if ($type == 'export_groups') {
            $items = ['0' => trans('front.ungrouped')] + GeofenceGroupRepo::getWhere(['user_id' => $this->user->id])->lists('title', 'id')->all();
        }
        elseif ($type == 'export_active') {
            $selected = GeofenceRepo::getWhere(['user_id' => $this->user->id, 'active' => 1])->lists('id', 'id')->all();
        }
        elseif ($type == 'export_inactive') {
            $selected = GeofenceRepo::getWhere(['user_id' => $this->user->id, 'active' => 0])->lists('id', 'id')->all();
        }

        $data = compact('items', 'selected', 'type');
        if ($this->api) {
            return $data;
        }
        else {
            $this->data = $type == 'export_groups' ? 'groups' : 'geofences';
            
            $input = $this->data;
            
            return view('front::Geofences.exportType')->with(array_merge($data, compact('input')));
        }
    }

    public function destroy()
    {
        if (!$this->user->perm('geofences', 'remove'))
            return ['status' => 0, 'perm' => 0];

        $id = array_key_exists('geofence_id', $this->data) ? $this->data['geofence_id'] : $this->data['id'];
        $item = GeofenceRepo::find($id);
        if (empty($item) || $item->user_id != $this->user->id)
            return ['status' => 0, 'errors' => ['polygon' => dontExist('front.geofence')]];

        $devices = DB::table('geofences')
            ->select('devices.imei')
            ->join('alert_geofence', 'geofences.id', '=', 'alert_geofence.geofence_id')
            ->join('alert_device', 'alert_geofence.alert_id', '=', 'alert_device.alert_id')
            ->join('devices', 'alert_device.device_id', '=', 'devices.id')
            ->where('geofences.id', '=', $item->id)
            ->groupBy('devices.id')
            ->get();

        if (!empty($devices)) {
            foreach ($devices as $device)
                clearCache($device->imei, 'alerts');
        }

        GeofenceRepo::delete($id);
        
        return ['status' => 1];
    }

    public function getGeofence(){
    try{
       $valid =  Validator::make($this->data, [
            'geofence_id' => 'required|exists:geofences,id'
            ]);
        if($valid->fails()){
            return ['status'=> 0 , 'message' => $valid->errors()->first()];
        }else{
            $item = GeofenceRepo::find($this->data['geofence_id'])->toArray();
           // print_r($item);
            echo(json_encode(array("status" => '1' , 'geofence' => array('id' => $item['id'] ,'user_id' => $item['user_id'] , 'active' => $item['active'] , 'name' => $item['name'] ,'coordinates'=> json_decode($item['coordinates'] , true) , 'polygon_color'=> $item['polygon_color'] )))); die;
        }
    }catch(\Exception $e){
        return ['status' => '0' , 'message' => $e->getMessage()];
    } 
    }

}