<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Facades\ModalHelpers\HistoryModalHelper;
use Tobuli\Helpers\GeoAddressHelper;

class HistoryController extends Controller
{
    public function index()
    {
        $data = HistoryModalHelper::get();

        return is_array($data) && !$this->api ? view('front::History.index')->with($data) : $data;
    }

    public function positionsPaginatedHistory()
    {
        $data = HistoryModalHelper::getMessages();

        return !$this->api ? view('front::History.partials.bottom_messages_history')->with($data) : $data;
    }

    public function positionsPaginated()
    {
        $data = HistoryModalHelper::getMessages();

        return !$this->api ? view('front::History.partials.bottom_messages')->with($data) : $data;
    }

    public function doDeletePositions()
    {
        return view('front::History.do_delete');
    }

    public function deletePositions()
    {
        HistoryModalHelper::deletePositions();

        return HistoryModalHelper::deletePositions();
    }
	
	public function getGeofenceHistory()
    {
		$latlng_arr = array();
		$device_id = $this->data['device_id'];
        $data = HistoryModalHelper::get();
		
		foreach($data['cords'] as $index => $cord)
		{
			if ( ! empty($cord['event']) ) continue;
			
			$latlng_arr[] = array('lat' => $cord['latitude'], 'lng' => $cord['longitude']);	
			//$device_id = $cord['device_id'];
				
		}
			
		return response()->json(array('coordinates' => $latlng_arr, 'device_id' => $device_id));
        //return is_array($data) && !$this->api ? view('front::History.index')->with($data) : $data;
    }
}
