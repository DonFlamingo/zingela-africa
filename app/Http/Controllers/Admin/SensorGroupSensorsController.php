<?php namespace App\Http\Controllers\Admin;

use Facades\Repositories\DeviceSensorRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use ModalHelpers\SensorModalHelper;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Repositories\SensorGroup\SensorGroupRepositoryInterface as SensorGroup;
use Tobuli\Repositories\SensorGroupSensor\SensorGroupSensorRepositoryInterface as SensorGroupSensor;
use Tobuli\Validation\AdminSensorGroupFormValidator;

class SensorGroupSensorsController extends BaseController {

    public function index($id, $ajax = 0, SensorGroupSensor $sensorGroupSensorRepo) {
        $items = $sensorGroupSensorRepo->getWhere(['group_id' => $id], 'name');

        $sensors_arr = Config::get('tobuli.sensors');

        foreach ($items as &$item)
            $item->type_title = $sensors_arr[$item->type];

        return view('admin::SensorGroupSensors.'.($ajax ? 'table' : 'index'))->with(compact('items', 'id'));
    }

    public function create($id, SensorModalHelper $sensorModalHelper) {
        $data = array_merge($sensorModalHelper->createData(null), [
            'route' => 'admin.sensor_group_sensors.store',
            'id' => $id
        ]);

        return view('front::Sensors.create')->with($data);
    }
    
    public function store(Request $request, SensorModalHelper $sensorModalHelper, SensorGroup $sensorGroupRepo, SensorGroupSensor $sensorGroupSensorRepo) {
        $input = $request->all();

        try
        {
            $sensorModalHelper->validate($input, 'create', null);

            $arr = $sensorModalHelper->formatInput($input, Auth::User()->id);
            $arr['group_id'] = $input['id'];

            $sensorGroupSensorRepo->create($arr);

            $count = $sensorGroupSensorRepo->countwhere(['group_id' => $arr['group_id']]);

            $sensorGroupRepo->update($arr['group_id'], [
                'count' => $count
            ]);

            return ['status' => 1];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    public function edit($id, SensorGroupSensor $sensorGroupSensorRepo, SensorModalHelper $sensorModalHelper) {
        $data = array_merge($sensorModalHelper->createData(null), [
            'route' => 'admin.sensor_group_sensors.update',
            'id' => $id
        ]);

        $data['item'] = $sensorGroupSensorRepo->find($id);

        return view('front::Sensors.edit')->with($data);
    }

    public function update(Request $request, SensorModalHelper $sensorModalHelper, SensorGroupSensor $sensorGroupSensorRepo) {
        $input = $request->all();

        try
        {
            $sensorModalHelper->validate($input, 'create', null);

            $arr = $sensorModalHelper->formatInput($input, Auth::User()->id);

            $sensorGroupSensorRepo->update($input['id'], $arr);

            return ['status' => 1];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    public function destroy(Request $request, SensorGroup $sensorGroupRepo, SensorGroupSensor $sensorGroupSensorRepo) {
        $input = $request->all();
        if (!isset($input['id']) || empty($input['id']))
            return response()->json(['status' => 0]);

        $ids = $input['id'];
        $id = current($ids);
        $item = $sensorGroupSensorRepo->find($id);

        $sensorGroupSensorRepo->deleteWhereIn($ids);

        $count = $sensorGroupSensorRepo->countwhere(['group_id' => $item->group_id]);
        $sensorGroupRepo->update($item->group_id, [
            'count' => $count
        ]);

        return response()->json(['status' => 1, 'trigger' => 'updateSensorGroupsTable']);
    }
}
