<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Facades\ModalHelpers\MapIconModalHelper;
use Tobuli\Entities\MapIcon;

class MapIconsController extends Controller
{
    public function index()
    {
        $data = MapIconModalHelper::get();

        return !$this->api ? view('front::MapIcons.index')->with($data) : ['items' => $data];
    }

    public function getIcons()
    {
        $data = MapIconModalHelper::getIcons();

        if ($this->api && !$this->user->perm('poi', 'edit'))
            return ['status' => 0, 'perm' => 0];

        return !$this->api ? view('front::MapIcons.index')->with($data) : ['items' => $data];
    }


    public function store()
    {
        return MapIconModalHelper::create();
    }

    public function update()
    {
        return MapIconModalHelper::edit();
    }

    public function changeActive()
    {
        return MapIconModalHelper::changeActive();
    }

    public function iconsList()
    {
        return MapIconModalHelper::iconsList();
    }

    public function destroy()
    {
        return MapIconModalHelper::destroy();
    }

    public function import_form()
    {
        $icons = MapIconModalHelper::getIcons();

        return view('front::MapIcons.import')->with(compact('icons'));
    }

    public function import()
    {
        $file = request()->file('file');

        $file_path = $file->getPathName();
        $content = file_get_contents($file_path);

        $data = MapIconModalHelper::import($content);

        return response()->json($data);
    }

}
