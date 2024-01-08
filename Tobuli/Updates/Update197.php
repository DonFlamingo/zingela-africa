<?php

use Illuminate\Support\Facades\DB;

class Update197 {
    use \Tobuli\Updates\UpdateTransport;

    function setUp()
    {
        DB::connection('traccar_mysql')->statement('ALTER TABLE devices MODIFY uniqueId VARCHAR(255) COLLATE utf8_unicode_ci;');
        $devices = DB::table('devices')->select('devices.traccar_device_id as id', 'devices.imei')->join('tracking_traccar.devices as traccar_devices', function ($query) {
            $query->on('devices.traccar_device_id', '=', 'traccar_devices.id');
            $query->on('devices.imei', '!=', 'traccar_devices.uniqueId');
        })->get();
        foreach ($devices as $device)
            DB::connection('traccar_mysql')->table('devices')->where('id', '=', $device->id)->update(['uniqueId' => $device->imei]);

        DB::table('devices')->where('deleted', '=', 1)->delete();

        $devices = DB::connection('traccar_mysql')->table('devices')->select('devices.id')->leftJoin('tracking_web.devices as web_devices', function ($query) {
            $query->on('devices.id', '=', 'web_devices.traccar_device_id');
        })->whereNull('web_devices.id')->get();
        foreach ($devices as $device)
            DB::connection('traccar_mysql')->table('devices')->where('id', '=', $device->id)->delete();

        return 'OK';
    }
}