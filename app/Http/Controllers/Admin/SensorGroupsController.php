<?php namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Repositories\SensorGroup\SensorGroupRepositoryInterface as SensorGroup;
use Tobuli\Validation\AdminSensorGroupFormValidator;

class SensorGroupsController extends BaseController {

    public function index(Request $request, SensorGroup $sensorGroupRepo) {
        $items = $sensorGroupRepo->getWhere([], 'title');

        return view('admin::SensorGroups.' . ($request->ajax() ? 'table' : 'index'))->with(compact('items'));
    }

    public function create() {
        return view('admin::SensorGroups.create');
    }
    
    public function store(Request $request, SensorGroup $sensorGroupRepo, AdminSensorGroupFormValidator $adminSensorGroupFormValidator) {
        $input = $request->all();
        try {
            $adminSensorGroupFormValidator->validate('create', $input);

            beginTransaction();
            try {
                $sensorGroupRepo->create($input);

            } catch (\Exception $e) {
                rollbackTransaction();
                throw new ValidationException(['id' => trans('global.unexpected_db_error')]);
            }
            commitTransaction();

            return response()->json(['status' => 1]);
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    public function edit($id, SensorGroup $sensorGroupRepo) {
        $item = $sensorGroupRepo->find($id);

        return view('admin::SensorGroups.edit')->with(compact('item'));
    }

    public function update(Request $request, SensorGroup $sensorGroupRepo, AdminSensorGroupFormValidator $adminSensorGroupFormValidator) {
        $input = $request->all();
        try {
            $adminSensorGroupFormValidator->validate('update', $input, $input['id']);

            beginTransaction();
            try {
                $sensorGroupRepo->update($input['id'], $input);

            } catch (\Exception $e) {
                rollbackTransaction();
                throw new ValidationException(['id' => trans('global.unexpected_db_error')]);
            }
            commitTransaction();

            return response()->json(['status' => 1]);
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    public function destroy(Request $request, SensorGroup $sensorGroupRepo) {
        $input = $request->all();
        if (!isset($input['id']) || empty($input['id']))
            return response()->json(['status' => 0]);

        $ids = $input['id'];

        $sensorGroupRepo->deleteWhereIn($ids);

        return response()->json(['status' => 1]);
    }
}
