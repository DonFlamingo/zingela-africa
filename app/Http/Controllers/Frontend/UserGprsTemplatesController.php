<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Facades\ModalHelpers\UserGprsTemplateModalHelper;

class UserGprsTemplatesController extends Controller
{
    public function index()
    {
        $data = UserGprsTemplateModalHelper::get();

        return !$this->api ? view('front::UserGprsTemplates.index')->with($data) : ['items' => $data];
    }

    public function create()
    {
        return view('front::UserGprsTemplates.create');
    }

    public function store()
    {
        return UserGprsTemplateModalHelper::create();
    }

    public function edit()
    {
        $data = UserGprsTemplateModalHelper::editData();

        return is_array($data) && !$this->api ? view('front::UserGprsTemplates.edit')->with($data) : $data;
    }

    public function update()
    {
        return UserGprsTemplateModalHelper::edit();
    }

    public function getMessage()
    {
        $data = UserGprsTemplateModalHelper::getMessage();

        return isset($data['message']) ? $data['message'] : '';
    }

    public function doDestroy($id)
    {
        $data = UserGprsTemplateModalHelper::doDestroy($id);

        return is_array($data) ? view('front::UserGprsTemplates.destroy')->with($data) : $data;
    }

    public function destroy()
    {
        return UserGprsTemplateModalHelper::destroy();
    }
}
