<?php namespace ModalHelpers;

use Facades\ModalHelpers\HistoryModalHelper;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Helpers\HistoryHelper;

class HistoryExportModalHelper extends ModalHelper {
	
	public static $base_path;
	
	public function __construct()
	{
		parent::__construct();
		
        self::$base_path = base_path('storage/logs/');
    }

	public function get()
	{
		$format	= request()->get('format', '');
	
		$data	= HistoryModalHelper::get();
		
		//error
		if ( is_string( $data ) ) {
			$error = $data;
		} 
		elseif( empty($data['items'])  )
		{
			$error = trans('front.no_history');
		}
		
		
		if ( empty( $error ) )
		{
			$date_from	= tdate(request()->get('from_date', '').' '.request()->get('from_time', ''));
			$date_to	= tdate(request()->get('to_date', '').' '.request()->get('to_time', ''));
			$device		= $data['device'];
			$filename	= $device->name . ' ' . str_replace( ':', '-', $date_from . ' - ' . $date_to );
			$filename	= str_replace( ['../','/',' '], ['','-','_'], $filename );
			$file		= md5( serialize(request()->all()) );
			$path		= self::$base_path . $file;
		
			$fp		= fopen( $path, 'w' );
		
			switch ( $format ) 
			{
				case 'gsr':
					$filename .= '.gsr';
					
					$json = [
						'gsr' => '0.2v',
						'imei' => $device->imei,
						'name' => $device->name,
						'route' => []
					];
					
					$i = 0;
					foreach ( $data['cords'] as $index => $cord )
					{
						if ( ! empty($cord['event']) ) continue;
						
						$data['cords'][$index]['index'] = $i++;
						
						$json['route']['route'][] = [
							$cord['time'],
							$cord['lat'],
							$cord['lng'],
							$cord['altitude'],
							$cord['course'],
							$cord['speed'],
						];
					}
					
					foreach ( $data['items'] as $item )
					{
						switch ($item['status'])
						{
							case '1': //drive
								reset($item['items']);
								$first_drive_cord = $data['cords'][ key($item['items']) ];
								end($item['items']);
								$last_drive_cord = $data['cords'][ key($item['items']) ];
								
								$last_stop_cord = empty( $last_stop_cord ) ? $first_drive_cord : $last_stop_cord;
								
								$json['route']['drives'][] = [
									$first_drive_cord['index'], //first drive cord index
									$last_stop_cord['index'], //last stop cord index
									$last_drive_cord['index'], //last drive cord index
									$first_drive_cord['time'], //first drive cord time 
									$last_stop_cord['time'], //last stop cord time
									$last_drive_cord['time'], //last drive cord time
									$item['time'], 
									$item['distance'],
									$item['top_speed'],
									$item['average_speed'],
									$item['fuel_consumption'],
									0.00, //??
									0, //??
								];
								break;
							case '2': //stop
								reset($item['items']);
								$first_stop_cord = $data['cords'][ key($item['items']) ];
								end($item['items']);
								$last_stop_cord = $data['cords'][ key($item['items']) ];
								
								$json['route']['stops'][] = [
									$first_stop_cord['index'], //first stop cord index
									$last_stop_cord['index'], //last stop cord index
									$first_stop_cord['lat'],
									$first_stop_cord['lng'],
									$first_stop_cord['altitude'],
									$first_stop_cord['course'],
									$first_stop_cord['time'], //first stop cord time
									$last_stop_cord['time'], //last stop cord time
									$item['time'], 
									0, //??
									[], //??
								];
								break;
							case '3': //start
								break;
							case '4': //end
								break;
							case '5': //event
								reset($item['items']);
								$first_event_cord = $data['cords'][ key($item['items']) ];
								
								$json['route']['events'][] = [
									$first_event_cord['message'], //name
									$first_event_cord['time'],
									$first_event_cord['lat'],
									$first_event_cord['lng'],
									$first_event_cord['altitude'],
									$first_event_cord['course'],
									$first_event_cord['speed'],
								];
								break;
						}
						
						$json['route_length'] = $data['distance_sum'];
						$json['top_speed'] = $data['top_speed'];
						$json['avg_speed'] = 0;
						$json['fuel_consumption'] = $data['fuel_consumption'];
						$json['fuel_cost'] = 0;//$data['fuel_cost'];
						$json['stops_duration'] = $data['stop_duration'];
						$json['drives_duration'] = $data['move_duration'];
						$json['engine_work'] = '0 s';
						$json['engine_idle'] = '0 s';
					}
					
					fwrite ( $fp , json_encode( $json ) );
					
					break;
				case 'kml':
					$filename .= '.kml';
					
					$xml  = '<?xml version="1.0" encoding="UTF-8"?>';
					$xml .= '<kml xmlns="http://www.opengis.net/kml/2.2">';
					$xml .= '<Document>';
					$xml .= '<name>'.$device->name.'</name>';
					$xml .= '<Style id="style1"><LineStyle><color>#F0000E6</color><width>4</width></LineStyle></Style>';
					$xml .= '<Placemark>';
					$xml .= '<name><![CDATA[Track from '.$date_from.' to '.$date_to.'  UTC]]></name>';
					$xml .= '<styleUrl>#style1</styleUrl>';
					$xml .= '<MultiGeometry>';
					$xml .= '<LineString>';
					$xml .= '<tessellate>1</tessellate>';
					$xml .= '<altitudeMode>clampToGround</altitudeMode>';
					$xml .= '<coordinates>';
					fwrite ( $fp , $xml );
					
					foreach ( $data['cords'] as $cord )
					{
						if ( ! empty($cord['event']) ) continue;
						
						$xml = $cord['longitude'] . ',' . $cord['latitude'] . ',' . $cord['altitude'] . ' ';
						fwrite ( $fp , $xml );
					}
			
					$xml  = '</coordinates>';
					$xml .= '</LineString>';
					$xml .= '</MultiGeometry>';
					$xml .= '</Placemark>';
					$xml .= '</Document>';
					$xml .= '</kml>';
								
					fwrite ( $fp , $xml );
					
					break;
				case 'gpx':
					$filename .= '.gpx';
					
					$xml  = '<?xml version="1.0" encoding="UTF-8"?>';
					$xml .= '<gpx creator="atrams.com" version="1.0" xmlns="http://www.topografix.com/GPX/1/0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd">';
					$xml .= '<trk>';
					$xml .= '<name>Track from '.$date_from.' to '.$date_to.'  UTC</name>';
					$xml .= '<type>GPS Tracklog</type>';
					$xml .= '<trkseg>';
					fwrite ( $fp , $xml );
					
					$i = 0;
					foreach ( $data['cords'] as $cord )
					{
						if ( ! empty($cord['event']) ) continue;
						
						$xml  = '<trkpt lat="'.$cord['latitude'].'" lon="'.$cord['longitude'].'">';
						$xml .= '<speed>'.$cord['speed'].'</speed>';
						$xml .= '<ele>'.$i++.'</ele>';
						$xml .= '<time>'.$cord['time'].'</time>';
						$xml .= '</trkpt>';
						fwrite ( $fp , $xml );
					}
			
					$xml  = '</trkseg>';
					$xml .= '</trk>';
					$xml .= '</gpx>';
								
					fwrite ( $fp , $xml );
					
					break;
				case 'csv':
					$filename .= '.csv';

                    $params = json_decode($data['device']['parameters'], true);
                    $params = $params ? array_combine($params, $params) : [];

					//dt,lat,lng,altitude,angle,speed,params
					$fields = array_merge([
						'dt' => 'time',
						'lat' => 'latitude',
						'lng' => 'longitude',
						'altitude' => 'altitude',
						'angle' => 'course',
						'speed' => 'speed',
						//'params' => 'other_arr'
					], $params);
									
					//heading
					fputcsv($fp, array_keys( $fields ));
					
					$fields_data = array_values( $fields );
				
					foreach ( $data['cords'] as $cord )
					{
						if ( ! empty($cord['event']) ) continue;

                        $others = [];
                        foreach ($cord['other_arr'] as $other_val) {
                            list($key, $val) = explode(':', $other_val);
                            $others[trim($key)] = trim($val);
                        }

                        $cord = array_merge($cord, $others);
						
						$cord = array_only($cord, $fields_data);

						//sort array by headings
						$cord = array_merge( array_flip($fields_data), $cord );
			
						fputcsv( $fp, $cord );

                        unset($cord);
					}
					
					break;
					
				default:
					break;
			}
			
			fclose( $fp );
			
			
			return [ 'download' => route('history.download', [$file, urlencode($filename)]) ];
		} else {
			return [ 'error' => $error ];
		}
	}
	
	public function getFile( $file ) {
		$file = str_replace('..', '', $file);
		
		return self::$base_path . $file;
	}
}