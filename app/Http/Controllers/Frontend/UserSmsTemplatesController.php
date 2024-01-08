<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Facades\ModalHelpers\UserSmsTemplateModalHelper;

class UserSmsTemplatesController extends Controller
{
    public function index()
    {
        $data = UserSmsTemplateModalHelper::get();

        return !$this->api ? view('front::UserSmsTemplates.index')->with($data) : ['items' => $data];
    }

    public function create()
    {
        return view('front::UserSmsTemplates.create');
    }

    public function store()
    {
        return UserSmsTemplateModalHelper::create();
    }

    public function edit()
    {
        $data = UserSmsTemplateModalHelper::editData();

        return is_array($data) && !$this->api ? view('front::UserSmsTemplates.edit')->with($data) : $data;
    }

    public function update()
    {
        return UserSmsTemplateModalHelper::edit();
    }

    public function getMessage()
    {
        $data = UserSmsTemplateModalHelper::getMessage();

        return isset($data['message']) ? (!$this->api ? $data['message'] : $data) : '';
    }

    public function doDestroy($id)
    {
        $data = UserSmsTemplateModalHelper::doDestroy($id);

        return is_array($data) ? view('front::UserSmsTemplates.destroy')->with($data) : $data;
    }

    public function destroy()
    {
        return UserSmsTemplateModalHelper::destroy();
    }
}
