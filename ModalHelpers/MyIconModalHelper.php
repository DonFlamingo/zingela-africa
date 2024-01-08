<?php namespace ModalHelpers;

use Illuminate\Support\Facades\File;
use Tobuli\Repositories\DeviceIcon\DeviceIconRepositoryInterface as DeviceIcon;
use Tobuli\Repositories\Device\DeviceRepositoryInterface as Device;
use Tobuli\Exceptions\ValidationException;

class MyIconModalHelper {
    /**
     * @var DeviceIcon
     */
    private $deviceIcon;
    /**
     * @var Device
     */
    private $device;

    function __construct(DeviceIcon $deviceIcon, Device $device) {
        $this->deviceIcon = $deviceIcon;
        $this->device = $device;
    }

    public function createData($user) {
        $icons = $this->deviceIcon->getWhere(['user_id' => $user->id]);

        return compact('icons');
    }

    public function create($icons, $user) {
        try {
            if (isLimited($user, 'my_icons'))
                throw new ValidationException(['icons[]' => trans('front.limited_acc')]);

            $icons_nr = $this->deviceIcon->countwhere(['user_id' => $user->id]) + count($icons);
            if ($icons_nr > 10)
                throw new ValidationException(['icons[]' => trans('front.my_icons_limit')]);

            if (count($icons)) {
                foreach ($icons as $file) {
                    if (empty($file))
                        continue;
                    list($w, $h) = getimagesize($file);
                    if (!$w)
                        throw new ValidationException(['icons[]' => trans('front.not_image')]);
                }

                foreach ($icons as $file) {
                    if (empty($file))
                        continue;
                    $destinationPath = 'images/device_icons';
                    $filename = uniqid('', TRUE).'-'.$user->id.'.'.$file->getClientOriginalExtension();
                    $file->move($destinationPath, $filename);
                    $this->deviceIcon->create([
                        'path' => $destinationPath.'/'.$filename,
                        'width' => $w,
                        'height' => $h,
                        'user_id' => $user->id
                    ]);
                }
            }

            return ['status' => 1];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    public function destroy($id, $user) {
        $del_icon = $this->deviceIcon->find($id);
        if ($del_icon && $del_icon->user_id == $user->id) {
            $ids = [$id => $id];
            $icon = $this->deviceIcon->whereNotInFirst($ids);
            $this->device->updateWhereIconIds($ids, ['icon_id' => $icon->id]);
            $filename = public_path().'/'.$del_icon->path;
            if (File::exists($filename)) {
                File::delete($filename);
            }
            $this->deviceIcon->delete($id);
        }

        return ['status' => 1];
    }
}