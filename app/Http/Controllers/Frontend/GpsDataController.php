<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Curl;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Tobuli\Repositories\Device\DeviceRepositoryInterface as Device;

class GpsDataController extends Controller {

    /**
     * @var Device
     */
    private $device;

    function __construct(Device $device) {
        $this->device = $device;
    }

    public function insert() {
        $input = Input::all();
        $error = null;
        $required = ['imei' => '', 'date' => '', 'lat' => '', 'lon' => '', 'speed' => '', 'altitude' => '', 'course' => '', 'protocol' => ''];

        foreach ($required as $field => $value) {
            if (!isset($input[$field]))
                $error .= $field.', ';
        }

        $other_arr = array_diff_key($input, $required);

        if (!is_null($error))
            return Response::make(json_encode(['status' => 0, 'message' => 'Missing params: '.substr($error, 0, -2)]), 400);

        $device = $this->device->findWhere(['imei' => $input['imei']]);
        if (empty($device))
            return Response::make(json_encode(['status' => 0, 'message' => 'IMEI not found']), 400);

        $data = [
            'fixTime'    => strtotime($input['date']) * 1000,
            'valid'      => 1,
            'deviceId'   => $input['imei'],
            'latitude'   => $input['lat'],
            'longitude'  => $input['lon'],
            'attributes' => $other_arr,
            'speed'      => $input['speed'] / 1.852,
            'altitude'   => $input['altitude'],
            'course'     => $input['course'],
            'protocol'   => $input['protocol'],
        ];

        $key = 'position.'.$data['fixTime'].'.'.$data['deviceId'];

        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
        $redis->set($key, json_encode($data));

        /*
        $curl = new Curl;
        $curl->follow_redirects = false;
        $curl->options['CURLOPT_SSL_VERIFYPEER'] = false;

        $response = $curl->get('http://127.0.0.1/insert.php', [
            'key' => env('key', 'Hdaiohaguywhga12344hdsbsdsfsd'),
            'fixTime' => strtotime($input['date']) * 1000,
            'valid' => 1,
            'uniqueId' => $input['imei'],
            'latitude' => $input['lat'],
            'longitude' => $input['lon'],
            'attributes' => json_encode($other_arr),
            'speed' => $input['speed'] / 1.852,
            'altitude' => $input['altitude'],
            'course' => $input['course'],
            'protocol' => $input['protocol'],
        ]);
        */

        return Response::make(json_encode(['status' => 1, 'message' => 'OK']), 200);
    }

}
