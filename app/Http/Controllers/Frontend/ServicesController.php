<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Facades\ModalHelpers\ServiceModalHelper;
use Facades\Repositories\DeviceServiceRepo;
use Illuminate\Support\Facades\Form;

class ServicesController extends Controller
{
    public function index($device_id = NULL)
    {
        $data = ServiceModalHelper::paginated();
        if (!$this->api) {
            $data = [
                'services' => $data,
                'device_id' => $device_id
            ];
        }
        
        return !$this->api ? view('front::Services.index')->with($data) : $data;
    }

    public function create() {
        $data = ServiceModalHelper::createData();
        
        return !$this->api ? view('front::Services.create')->with($data) : $data;
    }

    public function store() {
        return ServiceModalHelper::create();
    }

    public function edit() {
        $data = ServiceModalHelper::editData();

        return is_array($data) && !$this->api ? view('front::Services.edit')->with($data) : $data;
    }

    public function update() {
        return ServiceModalHelper::edit();
    }

    public function doDestroy($id) {
        $item = DeviceServiceRepo::find($id);
        if (empty($item) || !$item->device->users->contains($this->user->id))
            return modal(dontExist('front.service'), 'danger');

        return view('front::Services.destroy')->with(compact('item'));
    }

    public function destroy() {
        return ServiceModalHelper::destroy();
    }
}
