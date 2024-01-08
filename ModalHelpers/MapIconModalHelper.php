<?php namespace ModalHelpers;

use Facades\Repositories\MapIconRepo;
use Facades\Repositories\UserMapIconRepo;
use Facades\Validators\UserMapIconFormValidator;
use Tobuli\Exceptions\ValidationException;

class MapIconModalHelper extends ModalHelper
{
    public function get()
    {
        $mapIcons = UserMapIconRepo::whereUserId($this->user->id);
        if (!$this->user->perm('poi', 'view'))
            $mapIcons = [];

        return compact('mapIcons');
    }

    public function getIcons()
    {
        $mapIcons = MapIconRepo::all();
        if (!$this->api && !$this->user->perm('poi', 'view'))
            $mapIcons = [];

        return $mapIcons;
    }

    public function iconsList()
    {
        $mapIcons = MapIconRepo::all();

        return view('front::MapIcons._list', compact('mapIcons'));
    }

    public function create()
    {
        try
        {
            if (!$this->api && !$this->user->perm('poi', 'edit'))
                throw new ValidationException(['_token' => trans('front.dont_have_permission')]);

            $this->validate('create');
        
            if($this->data['coordinates'] == 'null') {
                throw new ValidationException(['coordinates' => 'No Coordinates set']);
            }

            UserMapIconRepo::create($this->data + ['user_id' => $this->user->id]);
            
            return ['status' => 1, 'data' => $this->data['coordinates']];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    public function edit()
    {
        $item = UserMapIconRepo::find($this->data['id']);

        try
        {
            if (!$this->api && !$this->user->perm('poi', 'edit'))
                throw new ValidationException(['_token' => trans('front.dont_have_permission')]);

            if (empty($item) || $item->user_id != $this->user->id)
                throw new ValidationException(['coordinates' => dontExist('front.marker')]);

            $this->validate('update');

            UserMapIconRepo::update($item->id, $this->data);
            
            return ['status' => 1];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    private function validate($type)
    {
        // If demo acc
        if ($this->user->id == 0)
            throw new ValidationException(['id' => trans('front.demo_acc')]);

        // Limited acc
        if (isLimited($this->user, 'poi'))
            throw new ValidationException(['id' => trans('front.limited_acc')]);

        UserMapIconFormValidator::validate($type, $this->data);
    }

    public function changeActive()
    {
        if (!$this->api && !$this->user->perm('poi', 'edit'))
            return ['status' => 1];

        $id = array_key_exists('map_icon_id', $this->data) ? $this->data['map_icon_id'] : $this->data['id'];
        
        $item = UserMapIconRepo::find($this->data['id']);
        if (empty($item) && $item->user_id == $this->user->id)
            return ['status' => 0, 'errors' => ['polygon' => dontExist('front.marker')]];

        UserMapIconRepo::update($item->id, ['active' => ($this->data['active'] == 'true')]);
        
        return ['status' => 1];
    }

    public function destroy()
    {
        if (!$this->user->perm('poi', 'remove'))
            return ['status' => 0, 'perm' => 0];

        $id = array_key_exists('map_icon_id', $this->data) ? $this->data['map_icon_id'] : $this->data['id'];

        $item = UserMapIconRepo::find($id);
        if (empty($item) || $item->user_id != $this->user->id)
            return ['status' => 0, 'errors' => ['polygon' => dontExist('front.marker')]];

        UserMapIconRepo::delete($id);
        
        return ['status' => 1];
    }

    public function import($content = NULL, $map_icon_id = NULL)
    {
        if (is_null($content))
            $content = $this->data['content'];

        if (is_null($map_icon_id))
            $map_icon_id = $this->data['map_icon_id'];

        libxml_use_internal_errors(true);

        $xml = simplexml_load_string($content);

        if (!$xml)
            return ['status' => 0, 'error' => trans('front.unsupported_format')];

        $icon_count = 0;
        $icon_exists_count = 0;

        try {
            // KML
            if ($xml) {
                $xml->registerXPathNamespace('kml', 'http://www.opengis.net/kml/2.2');

                $folders = $xml->xpath('//kml:Folder');

                if ( $folders ) {
                    foreach ( $folders as $folder ) {

                        foreach ( $folder->Placemark as $mark) {

                            $mark = json_decode(json_encode($mark), true);

                            if (empty($mark['name']))
                                continue;

                            if (empty($mark['Point']['coordinates']))
                                continue;

                            list($lng, $lat, $unknow) = explode(',', $mark['Point']['coordinates']);
                            $coordinates = ['lat' => $lat, 'lng' => $lng];

                            $item = UserMapIconRepo::findWhere(['coordinates' => json_encode($coordinates), 'user_id' => $this->user->id]);
                            if (empty($item)) {
                                $icon_count++;
                                UserMapIconRepo::create([
                                    'active'      => 0,
                                    'user_id'     => $this->user->id,
                                    'map_icon_id' => $map_icon_id,
                                    'name'        => $mark['name'],
                                    'description' => empty($mark['description']) ? '' : $mark['description'],
                                    'coordinates' => json_encode($coordinates)
                                ]);
                            }
                            else
                                $icon_exists_count++;
                        }
                    }
                } else {
                    foreach ( $xml->xpath('//kml:Placemark') as $mark) {
                        $mark = json_decode(json_encode($mark), true);

                        if (empty($mark['name']))
                            continue;

                        if (empty($mark['Point']['coordinates']))
                            continue;

                        list($lng, $lat, $unknow) = explode(',', $mark['Point']['coordinates']);
                        $coordinates = ['lat' => $lat, 'lng' => $lng];

                        $item = UserMapIconRepo::findWhere(['coordinates' => json_encode($coordinates), 'user_id' => $this->user->id]);
                        if (empty($item)) {
                            $icon_count++;
                            UserMapIconRepo::create([
                                'active'      => 0,
                                'user_id'     => $this->user->id,
                                'map_icon_id' => $map_icon_id,
                                'name'        => $mark['name'],
                                'description' => empty($mark['description']) ? '' : $mark['description'],
                                'coordinates' => json_encode($coordinates)
                            ]);
                        }
                        else
                            $icon_exists_count++;
                    }
                }
            }
        }
        catch (\Exception $e) {
            return ['status' => 0, 'error' => trans('front.unsupported_format')];
        }

        return array_merge(['status' => 1, 'message' => strtr(trans('front.imported_map_icon'), [':count' => $icon_count])]);
    }
}