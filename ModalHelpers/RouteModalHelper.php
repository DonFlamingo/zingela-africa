<?php namespace ModalHelpers;

use Facades\Repositories\RouteRepo;
use Facades\Validators\RouteFormValidator;
use Tobuli\Exceptions\ValidationException;

class RouteModalHelper extends ModalHelper
{
    public function get()
    {
        $routes = RouteRepo::whereUserId($this->user->id);

        return compact('routes');
    }

    public function create()
    {
        try
        {
            if (!$this->api && !$this->user->perm('routes', 'edit'))
                throw new ValidationException(['_token' => trans('front.dont_have_permission')]);

            $this->validate('create');

            RouteRepo::create($this->data + ['user_id' => $this->user->id]);
            return ['status' => 1];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    public function edit()
    {
        $item = RouteRepo::find($this->data['id']);

        try
        {
            if (!$this->api && !$this->user->perm('routes', 'edit'))
                throw new ValidationException(['_token' => trans('front.dont_have_permission')]);

            if (empty($item) || $item->user_id != $this->user->id)
                throw new ValidationException(['polyline' => dontExist('front.route')]);

            $this->validate('update');

            RouteRepo::updateWithPolyline($item->id, $this->data);
            return ['status' => 1];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    private function validate($type)
    {
        RouteFormValidator::validate($type, $this->data);
    }

    public function changeActive()
    {
        if (!$this->api && !$this->user->perm('routes', 'edit'))
            return ['status' => 1];
        
        $item = RouteRepo::find($this->data['id']);
        if (empty($item) && $item->user_id == $this->user->id)
            return ['status' => 0, 'errors' => ['polyline' => dontExist('front.geofence')]];

        RouteRepo::update($item->id, ['active' => ($this->data['active'] == 'true')]);

        return ['status' => 1];
    }

    public function destroy()
    {
        if (!$this->api && !$this->user->perm('routes', 'remove'))
            return ['status' => 1];

        $id = array_key_exists('route_id', $this->data) ? $this->data['route_id'] : $this->data['id'];
        $item = RouteRepo::find($id);
        if (empty($item) || $item->user_id != $this->user->id)
            return ['status' => 0, 'errors' => ['polyline' => dontExist('front.geofence')]];

        RouteRepo::delete($id);

        return ['status' => 1];
    }
}