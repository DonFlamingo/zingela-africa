<?php namespace App\Http\Controllers\Frontend;

ini_set('memory_limit', '-1');
set_time_limit(0);

use App\Http\Controllers\Controller;
use Facades\ModalHelpers\ReportModalHelper;
use Facades\ModalHelpers\ReportLogModalHelper;

class ReportsController extends Controller
{

    public function index()
    {
        $data = ReportModalHelper::get();

        return !$this->api ? view('front::Reports.index')->with($data) : ['status' => 1, 'items' => $data];
    }

    public function create()
    {
        $data = ReportModalHelper::createData();
		
		if ( is_array($data) )
		{
			$data['logs'] = ReportLogModalHelper::get();
		}

        return is_array($data) && !$this->api ? view('front::Reports.create')->with($data) : $data;
    }

    public function store()
    {
        return ReportModalHelper::create();
    }

    public function update()
    {
        $data = ReportModalHelper::generate();
        return $data;

//        return isset($this->data['generate']) ? $data : response()->json($data);
//        return isset($this->data['generate']) ? $data : response()->json($data);
    }

    public function doDestroy($id)
    {
        $data = ReportModalHelper::doDestroy($id);

        return is_array($data) ? view('front::Reports.destroy')->with($data) : $data;
    }

    public function destroy()
    {
        return ReportModalHelper::destroy();
    }
	
	public function logs()
    {
		$data['logs'] = ReportLogModalHelper::get();

        return view('front::Reports.logs')->with($data);
    }
	
	public function logDownload($id)
    {
		$data = ReportLogModalHelper::download($id);
	   
		return response()->make( $data['data'], 200, $data['headers']);
    }
	
	public function logDestroy()
    {
		$data = ReportLogModalHelper::destroy();

		return request()->ajax() ? response()->json( $data ) : null;
    }
}