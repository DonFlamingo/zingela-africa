<?php

use Illuminate\Support\Facades\DB;

class Update196 {
    use \Tobuli\Updates\UpdateTransport;

    function setUp() 
	{
        $item = DB::table('configs')->where('title', 'main_settings')->first();
        $settings = unserialize($item->value);

        if (isset($settings['google_api_key'])) {
            $settings['api_key'] = $settings['google_api_key'];
            unset($settings['google_api_key']);
        }

        DB::table('configs')->where('title', 'main_settings')->update([
            'value' => serialize($settings)
        ]);

        return 'OK';
    }
}