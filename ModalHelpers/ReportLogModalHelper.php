<?php namespace ModalHelpers;

use Facades\Repositories\ReportLogRepo;
use Facades\Repositories\UserRepo;
use Tobuli\Exceptions\ValidationException;

class ReportLogModalHelper extends ModalHelper
{
	private $types = [];
	private $formats = [];
	private $mimes = [];

	function __construct()
	{
		parent::__construct();

		$this->types = [
            '1'  => trans('front.general_information'),
            '2'  => trans('front.general_information_merged'),
            '16' => trans('front.general_information_merged_custom'),
            '3'  => trans('front.drives_and_stops'),
            '18' => trans('front.drives_and_stops').' / '.trans('front.geofences'),
            '19' => trans('front.drives_and_stops').' / '.trans('front.drivers'),
            '4'  => trans('front.travel_sheet'),
            '5'  => trans('front.overspeeds'),
            '6'  => trans('front.underspeeds'),
            '7'  => trans('front.geofence_in_out'),
            '15' => trans('front.geofence_in_out_24_mode'),
            '20' => trans('front.geofence_in_out').' ('.trans('front.ignition_on_off').')',
            '8'  => trans('front.events'),
            '10' => trans('front.fuel_level'),
            '11' => trans('front.fuel_fillings'),
            '12' => trans('front.fuel_thefts'),
            '13' => trans('front.temperature'),
			'14' => trans('front.rag'),
		];

		$this->formats = [
			'html' => trans('front.html'),
			'xls' => trans('front.xls'),
			'pdf' => trans('front.pdf'),
            'pdf_land' => trans('front.pdf_land'),
		];

		$this->mimes = [
			'html' => 'plain/text',
			'xls' => 'application/vnd.ms-excel',
			'pdf' => 'application/pdf',
            'pdf_land' => 'application/pdf',
		];

        $this->exts = [
            'html' => 'html',
            'xls' => 'xls',
            'pdf' => 'pdf',
            'pdf_land' => 'pdf',
        ];
	}

	public function get()
	{
		$filter	= [];
		if ($this->user->group_id == 2) {
			$filter	= ['user_id' => $this->user->id];
		}
		elseif ($this->user->group_id == 3) {
			$filter = ['user_ids' => UserRepo::getWhere(['manager_id' => $this->user->id])->lists('id', 'id')->all()];
			$filter['user_ids'][] = $this->user->id;
		}

		$logs		= ReportLogRepo::searchAndPaginate(['filter' => $filter], 'id', 'desc', 10);
		foreach ( $logs as $index => $log )
		{
			$logs[ $index ]->type_text = empty($this->types[ $log->type ]) ? $log->type : $this->types[ $log->type ];
			$logs[ $index ]->format_text = empty($this->formats[ $log->format ]) ? $log->format : $this->formats[ $log->format ];
		}

		return $logs;
	}

	public function download($id)
	{
		$where = isAdmin() ? ['id' => $id] : ['id' => $id, 'user_id' => $this->user->id];

		$log = ReportLogRepo::findWhere($where);

		if ( $log ) {
			$data = $log->data;

                        //print_r($data); exit;
                        
			$headers = [
				'Content-Type' => $this->mimes[ $log->format ],
				'Content-Length' => $log->size,
				'Content-Disposition' => 'attachment; filename="' . $log->title . '.' . $this->exts[ $log->format ] . '"'
			];
		}

		return compact('data', 'headers');
	}

	public function destroy()
	{
		if ( empty($this->data['id']) )
			return ['status' => 0, 'errors' => ['id' => dontExist('front.report')]];

		$ids = is_array( $this->data['id'] ) ? $this->data['id'] : [ $this->data['id'] ];

		$items = ReportLogRepo::getWhereIn( $ids );

		if ( empty($items) )
			return ['status' => 0, 'errors' => ['id' => dontExist('front.report')]];

		foreach ( $items as $item )
		{
			if ( ! isAdmin() && $item->user_id != $this->user->id )
				return ['status' => 0, 'errors' => ['id' => dontExist('front.report')]];

            $item->delete();
		}

		return ['status' => 1];
	}

}