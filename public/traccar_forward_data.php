<?php
/*
$log_dir = '/var/www/html/';
$log_name = "posts-" . $_GET['uniqueId'] . "-" . date("Y-m-d-H") . ".log";
$log_entry = gmdate('r') . "\t" . $_SERVER['REQUEST_URI'] . "
" . serialize($_GET) . "
" . $_GET['event']['type'] . "

";

$fp=fopen( $log_dir . $log_name, 'a' );

fputs($fp, $log_entry);
fclose($fp);
*/
//echo date_default_timezone_get();


//echo shell_exec("php -v");   
//echo exec("sudo service traccar restart");
//echo exec('systemctl stop traccar.service');

//echo exec('sudo systemctl stop traccar.service');
echo exec('sudo service traccar stop');
echo exec('sudo service traccar start');

//$ret = exec("php /var/www/html/tracking/artisan tracker:restart", $out, $err);
//var_dump($ret);
///var_dump($out);
//var_dump($err);

//echo shell_exec('curl -X POST -H "Content-Type: applicatin/json" -u admin:admin  --data '{"name":"test1","uniqueId":"test1"}' http://139.59.95.211:8082/api/devices');

//echo phpinfo();

/*
// Insert Device using PHP
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8082",
  CURLOPT_URL => "http://139.59.95.211:8082/api/devices",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\"name\":\"test2\",\"uniqueId\":\"test2\"}",
  CURLOPT_HTTPHEADER => array(
    "Accept: application/json",
    "Authorization: Basic YWRtaW46RHhiYWRtaW4xMg==",
    "Content-Type: application/json",
    "Postman-Token: 128e89b0-558f-4a44-9119-3b20b557cb3b",
    "cache-control: no-cache"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}

// Update Device using PHP
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8082",
  CURLOPT_URL => "http://139.59.95.211:8082/api/devices/49",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "PUT",
  CURLOPT_POSTFIELDS => "{\"id\":\"49\",\"name\":\"pinakin\",\"uniqueId\":\"1122334455\"}",
  CURLOPT_HTTPHEADER => array(
    "Accept: application/json",
    "Authorization: Basic YWRtaW46RHhiYWRtaW4xMg==",
    "Content-Type: application/json",
    "Postman-Token: 0cad2d8a-2467-4a87-93f5-63976325518b",
    "cache-control: no-cache"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}

// Delete Device using PHP
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_PORT => "8082",
  CURLOPT_URL => "http://139.59.95.211:8082/api/devices/71",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "DELETE",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
    "Accept: application/json",
    "Authorization: Basic YWRtaW46RHhiYWRtaW4xMg==",
    "Content-Type: application/json",
    "Postman-Token: 16a7310c-dbbd-49ff-b469-b08541c5798b",
    "cache-control: no-cache"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
*/

/*
// Laravel Update Device 
public function update(Request $request, $id){
        $data = [
            'id'       => $id, //add this
            'name'     => $request->name,
            'uniqueId' => $request->uniqueid,
            'model'    => $request->model,
            'contact'  => $request->contact,
            'phone'    => $request->phonecontact,
            'category' => $request->category,
        ];

        $device = new GuzzleHttp\Client([
            'base_uri' => 'http://domain:8082/api/devices/',
            'headers'  => ['content-type' => 'application/json', 'Accept' => 'application/json'],
            'body' => json_encode($data),
        ]);

        $res = $device->request('PUT', $id, ['auth' => [username, password]]);

        $status = $res->getStatusCode();
        if ($status == 200) {
            Flash::success(trans('content.alerts.deviceUpdateSuccess'))->important();  
            return redirect::to('devices');
        }else{
            Flash::success(trans('content.alerts.deviceUpdateError'))->important();  
            return redirect::to('devices');
        }
}
*/
?>