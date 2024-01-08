<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Facades\ModalHelpers\CustomEventModalHelper;
use Facades\ModalHelpers\SensorModalHelper;
use Illuminate\Html\FormFacade as Form;

class CustomEventsController extends Controller
{
    public function index()
    {
        $data = CustomEventModalHelper::get();

        return !$this->api ? view('front::CustomEvents.index')->with($data) : ['items' => $data];
    }

    public function create()
    {
        $data = CustomEventModalHelper::createData();

        return !$this->api ? view('front::CustomEvents.create')->with($data) : $data;
    }

    public function store()
    {
        return CustomEventModalHelper::create();
    }

    public function edit()
    {
        $data = CustomEventModalHelper::editData();

        return is_array($data) && !$this->api ? view('front::CustomEvents.edit')->with($data) : $data;
    }

    public function update()
    {
        return CustomEventModalHelper::edit();
    }

    public function getProtocols()
    {
        $protocols = SensorModalHelper::getProtocols();

        return !$this->api ? Form::select('event_protocol', $protocols, null, ['class' => 'form-control']) : apiArray($protocols);
    }

    public function getEvents()
    {
        $events = SensorModalHelper::getEvents();

        return !$this->api ? Form::select('event_id', $events, null, ['class' => 'form-control']) : apiArray($events);
    }

    public function doDestroy($id)
    {
        $data = CustomEventModalHelper::doDestroy($id);

        return is_array($data) ? view('front::CustomEvents.destroy')->with($data) : $data;
    }

    public function destroy()
    {
        return CustomEventModalHelper::destroy();
    }

}
