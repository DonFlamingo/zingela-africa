<?php namespace ModalHelpers;

use Illuminate\Support\Facades\Validator;
use Tobuli\Exceptions\ValidationException;

class GeofenceGroupsModalHelper extends ModalHelper {

    public function paginated($user, $api, $geofenceGroupRepo) {
        $groups = $geofenceGroupRepo->getWhere(['user_id' => $user->id]);

        if ($api) {
            $groups = $groups->toArray();
            $groups['url'] = route('api.get_geofence_groups');
        }

        return $groups;
    }

    public function edit($input, $user, $api, $geofenceGroupRepo) {
        $group_id = 0;
        if (!$api) {
            $edit_group = isset($input['edit_group']) ? $input['edit_group'] : [];
            $edit_arr = [];
            $groups_nr = 0;
            foreach ($edit_group as $id => $title) {
                if (empty($title))
                    continue;

                $edit_arr[$id] = $id;
                $geofenceGroupRepo->updateWhere(['id' => $id, 'user_id' => $user->id], ['title' => $title]);

                $groups_nr++;
            }

            $geofenceGroupRepo->deleteUsersWhereNotIn($edit_arr, $user->id);

            $add_group = isset($input['add_group']) ? $input['add_group'] : [];
            foreach ($add_group as $id => $title) {
                if (empty($title))
                    continue;

                $itemd = $geofenceGroupRepo->create(['title' => $title, 'user_id' => $user->id]);
                $group_id = $itemd->id;
                $groups_nr++;
                if ($groups_nr > 50)
                    break;
            }
        }
        else {
            $arr = [];
            $groups_nr = 0;
            $groups = $geofenceGroupRepo->getWhere(['user_id' => $user->id]);
            if (!$groups->isEmpty())
                $groups = $groups->lists('id', 'id')->all();

            $input_group = isset($input['groups']) ? json_decode($input['groups'], TRUE) : [];
            foreach ($input_group as $key => $group) {
                $title = $group['title'];
                $id = $group['id'];
                if (empty($title))
                    continue;

                if (array_key_exists($group['id'], $groups)) {
                    $arr[$id] = $id;
                    $geofenceGroupRepo->updateWhere(['id' => $id, 'user_id' => $user->id], ['title' => $title]);
                }
                else {
                    $itemd = $geofenceGroupRepo->create(['title' => $title, 'user_id' => $user->id]);
                    $id = $itemd->id;
                    $arr[$id] = $id;
                }

                $groups_nr++;
            }

            $geofenceGroupRepo->deleteUsersWhereNotIn($arr, $user->id);
        }

        return ['status' => 1, 'id' => $group_id];
    }
}