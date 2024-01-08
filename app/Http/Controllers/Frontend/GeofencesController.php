<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Facades\ModalHelpers\GeofenceModalHelper;

class GeofencesController extends Controller
{
   
    public function index()
    {
        $data = GeofenceModalHelper::get();

        return !$this->api ? view('front::Geofences.index')->with($data) : ['items' => $data];
    }

    public function create()
    {
        if (!$this->user->perm('geofences', 'edit'))
            return ['status' => 0, 'perm' => 0];

        return ['status' => 1];
    }

    public function store()
    {
        return GeofenceModalHelper::create();
    }

    public function update()
    {
        return GeofenceModalHelper::edit();
    }

    public function changeActive()
    {
        return GeofenceModalHelper::changeActive();
    }

    public function destroy()
    {
        return GeofenceModalHelper::destroy();
    }

    public function import()
    {
        $data = GeofenceModalHelper::import();

        return response()->json($data);
    }

    public function export()
    {
        $data = GeofenceModalHelper::exportData();

        return !$this->api ? view('front::Geofences.export')->with($data) : $data;
    }

    public function exportCreate()
    {
        $data = GeofenceModalHelper::export();

        header('Content-disposition: attachment; filename=geofences_export.gexp');
        header('Content-type: text/plain');

        echo json_encode($data);
    }

    public function exportType()
    {
        return GeofenceModalHelper::exportType();
    }

    public function getGeofence(){
         return  GeofenceModalHelper::getGeofence();
         
    }

}
