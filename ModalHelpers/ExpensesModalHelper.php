<?php namespace ModalHelpers;

use Facades\Repositories\DeviceSensorRepo;
use Facades\Repositories\DeviceExpensesRepo;
use Facades\Repositories\UserRepo;
use Facades\Validators\ExpensesFormValidator;
use Tobuli\Exceptions\ValidationException;

class ExpensesModalHelper extends ModalHelper
{
    public function get()
    {
        $expenses = DeviceExpensesRepo::searchAndPaginate(['filter' => ['user_id' => $this->user->id]], 'id', 'desc', 10);

        return compact('expenses');
    }
    public function createData()
    {
        $devices = UserRepo::getDevices($this->user->id);
        foreach ($devices as $key => $device) {
            if ($device['expiration_date'] != '0000-00-00' && strtotime($device['expiration_date']) < strtotime(date('Y-m-d')))
                unset($devices[$key]);
        }
        if (empty($devices))
            return $this->api ? ['status' => 0, 'errors' => ['id' => trans('front.no_devices')]] :
                view('front::Layouts.partials.modal_warning')->with(['type' => 'alert', 'message' => trans('front.no_devices')]);

        if ($this->api) {
            $reports = $reports->toArray();
            $reports['url'] = route('api.get_reports');
            $geofences = $geofences->toArray();
            foreach ($geofences as &$geo)
                unset($geo['polygon']);

            //devices list return as array, not object
            $devices = array_values($devices->all());
        }

        $expenses = DeviceExpensesRepo::searchAndPaginate(['filter' => ['user_id' => $this->user->id]], 'id', 'desc', 10);

        return compact('devices', 'expenses');
    }
    public function create(){
        try{
            $this->validate('create');
            $data = $this->formatInput();

            $expenses = DeviceExpensesRepo::create($data + ['user_id' => $this->user->id]);

            if (!empty($expenses)) {
                if (isset($this->data['devices']) && is_array($this->data['devices']) && !empty($this->data['devices']))
                    $expenses->devices()->sync($this->data['devices']);
            }

        }catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }

        return ['status' => $this->api ? 1 : 2];
    }

    private function validate($type) {
        ExpensesFormValidator::validate($type, $this->data);
    }

    private function formatInput() {
        $odometer = DeviceSensorRepo::findWhere([
            'device_id' => $this->data['devices'],
            'type' => 'odometer'
        ]);

        $engine_hours = DeviceSensorRepo::findWhere([
            'device_id' => $this->data['devices'],
            'type' => 'engine_hours'
        ]);

        if (!empty($odometer))
            $this->data['odometer'] = $odometer->odometer_value_by == 'virtual_odometer' ? $odometer->odometer_value : $odometer->value_formula;

        if (!empty($engine_hours))
            $this->data['engine_hours'] = $engine_hours->value;
        

        return $this->data;
    }

    public function doDestroy($id)
    {
        $item = DeviceExpensesRepo::find($id);
        if (empty($item) || $item->user_id != $this->user->id)
            return modal(dontExist('front.expenses'), 'danger');

        return compact('item');
    }

    public function destroy()
    {
        $id = array_key_exists('expenses_id', $this->data) ? $this->data['report_id'] : $this->data['id'];
        $item = DeviceExpensesRepo::find($id);
        if (empty($item) || $item->user_id != $this->user->id)
            return ['status' => 0, 'errors' => ['id' => dontExist('front.expenses')]];

            DeviceExpensesRepo::delete($id);

        return ['status' => 1];
    }
}