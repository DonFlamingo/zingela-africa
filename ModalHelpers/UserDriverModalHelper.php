<?php namespace ModalHelpers;

use Facades\Repositories\UserDriverRepo;
use Facades\Repositories\UserRepo;
use Facades\Validators\UserDriverFormValidator;
use Illuminate\Support\Facades\DB;
use Tobuli\Exceptions\ValidationException;

class UserDriverModalHelper extends ModalHelper
{
    public function get()
    {
        $drivers = UserDriverRepo::searchAndPaginate(['filter' => ['user_id' => $this->user->id]], 'id', 'desc', 5);
        $drivers->setPath(route('user_drivers.index'));

        if ($this->api) {
            $drivers = $drivers->toArray();
            $drivers['url'] = route('api.get_user_drivers');
        }

        return compact('drivers');
    }

    public function createData()
    {
        $devices = UserRepo::getDevices($this->user->id)->lists('name', 'id')->all();

        return compact('devices');
    }

    public function create()
    {
        try
        {
            $this->validate('create');

            $item = UserDriverRepo::create($this->data + ['user_id' => $this->user->id]);

            if (empty($this->data['rfid']) && isset($this->data['device_id'])) {
                $device_pivot = DB::table('user_device_pivot')->where(['user_id' => $this->user->id, 'device_id' => $this->data['device_id']])->first();
                if (empty($device_pivot->current_driver_id)) {
                    DB::table('user_device_pivot')->where(['user_id' => $this->user->id, 'device_id' => $this->data['device_id']])->update(['current_driver_id' => $item->id]);
                    DB::table('user_driver_position_pivot')->insert([
                        'device_id' => $this->data['device_id'],
                        'driver_id' => $item->id,
                        'date' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            clearCache($this->user->id, 'drivers');

            return ['status' => 1, 'item' => $item];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    public function editData()
    {
        $id = array_key_exists('user_driver_id', $this->data) ? $this->data['user_driver_id'] : request()->route('user_drivers');
        $item = UserDriverRepo::find($id);
        if (empty($item) || $item->user_id != $this->user->id)
            return $this->api ? ['status' => 0, 'errors' => ['id' => dontExist('front.driver')]] : modal(dontExist('front.driver'), 'danger');

        $devices = UserRepo::getDevices($this->user->id)->lists('name', 'id')->all();

        return compact('item', 'devices');
    }

    public function edit()
    {
        $item = UserDriverRepo::find($this->data['id']);

        try
        {
            if (empty($item) || $item->user_id != $this->user->id)
                throw new ValidationException(['id' => dontExist('front.driver')]);

            $this->validate('update');

            UserDriverRepo::update($item->id, $this->data);

            if (empty($this->data['rfid']) && isset($this->data['device_id'])) {
                $device_pivot = DB::table('user_device_pivot')->where(['user_id' => $this->user->id, 'device_id' => $this->data['device_id']])->first();
                if (empty($device_pivot->current_driver_id)) {
                    DB::table('user_device_pivot')->where(['user_id' => $this->user->id, 'device_id' => $this->data['device_id']])->update(['current_driver_id' => $item->id]);
                    DB::table('user_driver_position_pivot')->insert([
                        'device_id' => $this->data['device_id'],
                        'driver_id' => $item->id,
                        'date' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            clearCache($this->user->id, 'drivers');

            return ['status' => 1];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    private function validate($type)
    {
        UserDriverFormValidator::validate($type, $this->data);
    }

    public function doDestroy($id)
    {
        $item = UserDriverRepo::find($id);
        if (empty($item) || $item->user_id != $this->user->id)
            return modal(dontExist('front.driver'), 'danger');

        return compact('item');
    }

    public function destroy()
    {
        $id = array_key_exists('user_driver_id', $this->data) ? $this->data['user_driver_id'] : $this->data['id'];
        $item = UserDriverRepo::find($id);
        if (empty($item) || $item->user_id != $this->user->id)
            return ['status' => 0, 'errors' => ['polygon' => dontExist('front.driver')]];

        UserDriverRepo::delete($id);

        return ['status' => 1];
    }
}