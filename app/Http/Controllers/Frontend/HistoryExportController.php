<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Facades\ModalHelpers\HistoryExportModalHelper;

class HistoryExportController extends Controller
{
	public function generate()
	{

		$data = HistoryExportModalHelper::get();
		
		return response()->json($data);
	}
	
	public function download( $file, $filename )
	{
		$path = HistoryExportModalHelper::getFile($file);
		
		if ( ! file_exists($path) )
		{
			return;
		}
		
		return response()->download($path, $filename)->deleteFileAfterSend(true);
	}
}