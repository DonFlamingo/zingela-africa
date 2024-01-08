<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Html\FormFacade as Form;
use Tobuli\Repositories\GeofenceGroup\GeofenceGroupRepositoryInterface as GeofenceGroup;
use Tobuli\Repositories\User\UserRepositoryInterface as User;
use ModalHelpers\GeofenceGroupsModalHelper;

class GeofencesGroupsController extends Controller {

    public function index(GeofenceGroupsModalHelper $geofenceGroupsModalHelper, GeofenceGroup $geofenceGroupRepo) {
        $groups = $geofenceGroupsModalHelper->paginated(Auth::User(), 0, $geofenceGroupRepo);

        return view('front::GeofencesGroups.index')->with(compact('groups'));
    }

    public function store(GeofenceGroupsModalHelper $geofenceGroupsModalHelper, GeofenceGroup $geofenceGroupRepo) {
        $input = Input::all();

        return response()->json(array_merge($geofenceGroupsModalHelper->edit($input, Auth::User(), 0, $geofenceGroupRepo), ['trigger' => 'updateGeofenceGroupsSelect', 'url' => route('geofences_groups.update_select')]));
    }

    public function updateSelect(GeofenceGroup $geofenceGroupRepo) {
        $input = Input::all();
        $geofence_groups = $geofenceGroupRepo->getWhere(['user_id' => Auth::User()->id])->lists('title', 'id')->all();

        return Form::select('group_id', ['0' => trans('front.ungrouped')] + $geofence_groups, isset($input['group_id']) ? $input['group_id'] : null, ['class' => 'form-control']);
    }

    public function changeStatus(User $userRepo) {
        $input = Input::all();
        $geofence_groups_opened = array_flip(json_decode(Auth::User()->open_geofence_groups, TRUE));

        if (isset($geofence_groups_opened[$input['id']])) {
            unset($geofence_groups_opened[$input['id']]);
            $geofence_groups_opened = array_flip($geofence_groups_opened);
        }
        else {
            $geofence_groups_opened = array_flip($geofence_groups_opened);
            array_push($geofence_groups_opened, $input['id']);
        }

        $userRepo->update(Auth::User()->id, [
            'open_geofence_groups' => json_encode($geofence_groups_opened)
        ]);
    }

    public function destroy(GeofenceModalHelper $geofenceModalHelper, Geofence $geofenceRepo) {
        $id = Input::get('id');

        return response()->json($geofenceModalHelper->destroy($id, Auth::User(), $geofenceRepo));
    }

}
