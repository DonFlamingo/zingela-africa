<?php namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Tobuli\Repositories\DeviceIcon\DeviceIconRepositoryInterface as DeviceIcon;
use Tobuli\Repositories\Device\DeviceRepositoryInterface as Device;
use Tobuli\Validation\DeviceIconUploadValidator;
use Tobuli\Exceptions\ValidationException;

class DeviceIconsController extends BaseController {
    private $section = 'device_icons';
    /**
     * @var DeviceIcon
     */
    private $deviceIcon;
    /**
     * @var Device
     */
    private $device;
    /**
     * @var DeviceIconUploadValidator
     */
    private $deviceIconUploadValidator;

    function __construct(DeviceIcon $deviceIcon, Device $device, DeviceIconUploadValidator $deviceIconUploadValidator)
    {
        parent::__construct();
        $this->deviceIcon = $deviceIcon;
        $this->device = $device;
        $this->deviceIconUploadValidator = $deviceIconUploadValidator;
    }

    public function index() {
        $input = Input::all();

        $items = $this->deviceIcon->searchAndPaginate($input, 'path', 'desc', 40);
        $section = $this->section;
        $page = $items->currentPage();
        $total_pages = $items->lastPage();
        $pagination = smartPaginate($items->currentPage(), $total_pages);
        $url_path = $items->resolveCurrentPath();

        return View::make('admin::'.ucfirst($this->section).'.' . (Request::ajax() ? 'table' : 'index'))->with(compact('items', 'input', 'section', 'pagination', 'page', 'total_pages', 'url_path'));
    }

    public function store() {
        $file = Input::file('file');
        try
        {
            $this->deviceIconUploadValidator->validate('create', ['file' => $file]);
            $file = Input::file('file');
            list($w, $h) = getimagesize($file);
            $destinationPath = 'images/device_icons';
            $filename = uniqid('', TRUE).'.'.$file->getClientOriginalExtension();
            $file->move($destinationPath, $filename);
            $this->deviceIcon->create(['path' => $destinationPath.'/'.$filename, 'width' => $w, 'height' => $h]);

            /*$base_public_path = base_path('../../').'/public/frontend/images/device_icons';
            File::cleanDirectory($base_public_path);
            File::copyDirectory(base_path('public').'/'.$destinationPath, $base_public_path);*/
            return Response::json(['status' => 1]);
        }
        catch (ValidationException $e)
        {
            return Response::make($e->getErrors()->first(), '406');
        }
    }

    public function destroy() {
        $ids = Input::get('id');
        if (is_array($ids) && $nr = count($ids)) {
            $all = $this->deviceIcon->count();
            if ($nr >= $all) {
                return Response::json(['status' => 0, 'error' => trans('admin.cant_delete_all')]);
            }
            $icon = $this->deviceIcon->whereNotInFirst($ids);

            $this->device->updateWhereIconIds($ids, ['icon_id' => $icon->id]);
            foreach($ids as $id) {
                $del_icon = $this->deviceIcon->find($id);
                if ($del_icon) {
                    $filename = public_path().'/'.$del_icon->path;
                    if (File::exists($filename)) {
                        File::delete($filename);
                    }
                    $this->deviceIcon->delete($id);
                }
            }

            /*File::cleanDirectory(base_path('../../').'/public/frontend/images/device_icons');
            File::copyDirectory('frontend/images/device_icons', base_path('../../').'/public/frontend/images/device_icons');*/
        }

        return Response::json(['status' => 1]);
    }
}
