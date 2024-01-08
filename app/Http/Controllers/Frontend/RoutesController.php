<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Facades\ModalHelpers\RouteModalHelper;

class RoutesController extends Controller
{
    public function index()
    {
        $data = RouteModalHelper::get();

        return !$this->api ? view('front::Routes.index')->with($data) : $data;
    }

    public function store()
    {
        return RouteModalHelper::create();
    }

    public function update()
    {
        return RouteModalHelper::edit();
    }

    public function changeActive()
    {
        return RouteModalHelper::changeActive();
    }

    public function destroy()
    {
        return RouteModalHelper::destroy();
    }

}
