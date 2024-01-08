<?php namespace ModalHelpers;

use Facades\Repositories\DeviceSensorRepo;
use Facades\Repositories\DeviceServiceRepo;
use Facades\Validators\ServiceFormValidator;
use Illuminate\Support\Facades\Validator;
use Tobuli\Exceptions\ValidationException;

class ServiceModalHelper extends ModalHelper {

    public function paginated($device_id = NULL) {
        if (is_null($device_id)) {
            $device_id = array_key_exists('device_id', $this->data) ? $this->data['device_id'] : request()->route('device_id');
        }
        $services = DeviceServiceRepo::searchAndPaginate(['filter' => ['device_id' => $device_id]], 'id', 'desc', 10);

        $odometer = DeviceSensorRepo::findWhere([
            'device_id' => $device_id,
            'type' => 'odometer'
        ]);

        $engine_hours = DeviceSensorRepo::findWhere([
            'device_id' => $device_id,
            'type' => 'engine_hours'
        ]);

        $values = [
            'odometer' => [
                'value' => 0,
                'sufix' => ''
            ],
            'engine_hours' => [
                'value' => 0,
                'sufix' => ''
            ]
        ];

        if (!empty($odometer)) {
            $values['odometer']['value'] = $odometer->odometer_value_by == 'virtual_odometer' ? $odometer->odometer_value : $odometer->value_formula;
            $values['odometer']['sufix'] = $odometer->unit_of_measurement;
        }

        if (!empty($engine_hours)) {
            $values['engine_hours']['value'] = $engine_hours->value;
            $values['engine_hours']['sufix'] = $engine_hours->unit_of_measurement;
        }

        foreach ($services as &$service)
            $service->expires = serviceExpiration($service, $values);

        if ($this->api) {
            $services = $services->toArray();
            $services['url'] = route('api.get_services');
        }

        return $services;
    }

    public function createData($device_id = NULL) {
        if (is_null($device_id))
            $device_id = array_key_exists('device_id', $this->data) ? $this->data['device_id'] : request()->route('device_id');

        $odometer = DeviceSensorRepo::findWhere([
            'device_id' => $device_id,
            'type' => 'odometer'
        ]);

        $engine_hours = DeviceSensorRepo::findWhere([
            'device_id' => $device_id,
            'type' => 'engine_hours'
        ]);

        $odometer_value = '0';
        if (!empty($odometer))
            $odometer_value = $odometer->odometer_value_by == 'virtual_odometer' ? $odometer->odometer_value : $odometer->value_formula;

        $engine_hours_value = '0';
        if (!empty($engine_hours))
            $engine_hours_value = $engine_hours->value;

        $expiration_by = [
            'odometer' => trans('front.odometer'),
            'engine_hours' => trans('validation.attributes.engine_hours'),
            'days' => trans('validation.attributes.days')
        ];

        return compact('device_id', 'odometer_value', 'engine_hours_value', 'expiration_by');
    }

    public function create() {
        try
        {
            $this->validate('create');

            $this->data['zone'] = isset($this->user->timezone->zone) ? $this->user->timezone->zone : '+0 hours';
            $data = $this->formatInput();

            if (isset($this->data['expired']))
                throw new ValidationException(['id' => trans('front.service_already_expired')]);

            DeviceServiceRepo::create($data + ['user_id' => $this->user->id]);

            return ['status' => 1];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    public function editData() {
        $service_id = array_key_exists('service_id', $this->data) ? $this->data['service_id'] : request()->route('services');
        $item = DeviceServiceRepo::find($service_id);
        if (empty($item) || (!$item->device->users->contains($this->user->id) && !isAdmin()))
            return $this->api ? ['status' => 0, 'errors' => ['id' => dontExist('front.service')]] : modal(dontExist('front.service'), 'danger');

        $data = $this->createData($item->device_id);
        $data['item'] = $item;

        return $data;
    }

    public function edit() {
        $item = DeviceServiceRepo::find($this->data['id']);

        try
        {
            if (empty($item) || (!$item->device->users->contains($this->user->id) && !isAdmin()))
                throw new ValidationException(['id' => dontExist('front.service')]);

            $this->validate('update');

            $this->data['zone'] = isset($this->user->timezone->zone) ? $this->user->timezone->zone : '+0 hours';
            $input = $this->formatInput();

            if (isset($this->data['expired']))
                throw new ValidationException(['id' => trans('front.service_already_expired')]);

            DeviceServiceRepo::update($item->id, $input);

            return ['status' => 1];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    public function destroy() {
        $service_id = array_key_exists('service_id', $this->data) ? $this->data['service_id'] : $this->data['id'];
        $item = DeviceServiceRepo::find($service_id);
        if (empty($item) || !$item->device->users->contains($this->user->id))
            return ['status' => 0, 'errors' => ['id' => dontExist('front.service')]];

        DeviceServiceRepo::delete($item->id);
        return ['status' => 1];
    }

    private function validate($type) {
        ServiceFormValidator::validate($type, $this->data);

        $this->data['mobile_phone'] = isset($this->data['mobile_phone']) ? $this->data['mobile_phone'] : '';

        # Clean string, remove empty entries
        $arr['email'] = array_flip(explode(';', $this->data['email']));
        unset($arr['email']['']);
        $arr['email'] = array_flip($arr['email']);
        $arr['email'] = array_map('trim', $arr['email']);

        $arr['mobile_phone'] = array_flip(explode(';', $this->data['mobile_phone']));
        unset($arr['mobile_phone']['']);
        $arr['mobile_phone'] = array_flip($arr['mobile_phone']);

        # Regenerate string
        $this->data['email'] = implode(';', $arr['email']);
        $this->data['mobile_phone'] = implode(';', $arr['mobile_phone']);

        $validator = Validator::make($arr, ['email' => 'array_max:5']);
        $validator->each('email', ['email']);
        if ($validator->fails())
            throw new ValidationException(['email' => $validator->errors()->first()]);

        $validator = Validator::make($arr, ['mobile_phone' => 'array_max:5']);
        if ($validator->fails())
            throw new ValidationException(['mobile_phone' => $validator->errors()->first()]);
    }

    private function formatInput() {
        $odometer = DeviceSensorRepo::findWhere([
            'device_id' => $this->data['device_id'],
            'type' => 'odometer'
        ]);

        $engine_hours = DeviceSensorRepo::findWhere([
            'device_id' => $this->data['device_id'],
            'type' => 'engine_hours'
        ]);

        $values = [
            'odometer' => 0,
            'engine_hours' => 0
        ];

        if (!empty($odometer))
            $values['odometer'] = $odometer->odometer_value_by == 'virtual_odometer' ? $odometer->odometer_value : $odometer->value_formula;

        if (!empty($engine_hours))
            $values['engine_hours'] = $engine_hours->value;

        $this->data = prepareServiceData($this->data, $values);
        $this->data['renew_after_expiration'] = (isset($this->data['renew_after_expiration']) && $this->data['renew_after_expiration'] == 1);

        return $this->data;
    }
}