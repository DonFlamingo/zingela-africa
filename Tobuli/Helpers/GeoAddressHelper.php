<?php

namespace Tobuli\Helpers;

use Illuminate\Support\Facades\Cache;

class GeoAddressHelper {

    /**
     * Returns geoAddress by latitude and longitude,
     * first by looking into cache and if not found
     * makes call to geocoding API
     * @param $latOrig
     * @param $lonOrig
     * @return string|void
     */
    public function getGeoAddress($latOrig, $lonOrig) {
        $lat = $this->normalizeGeoValue($latOrig);
        $lon = $this->normalizeGeoValue($lonOrig);

        // Check first if geo address caching enabled in settings
        $cacheEnabled = (bool)settings('main_settings.geocoder_cache_enabled');
        if ($cacheEnabled) {
            $address = $this->findGeoAddressFromCache($lat, $lon);
            if (!empty($address)) {
                return $address;
            }
        }

        // geoAddress not found in cache - get it from API
        $address = $this->getGeoAddressFromApi($lat, $lon);
        if ($cacheEnabled && !empty($address)) {
            $this->saveGeoAddressToCache($lat, $lon, $address);
        }
        return $address;
    }

    /**
     * Deletes all cache items
     * @return bool
     */
    public function flushAllCache() {
        // Protection if Memcached not installed
        if (!class_exists('Memcached')) {
            return false;
        }

        try {
            Cache::store('memcached')->flush();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Returns geocoder cache memory usage in bytes
     * @return int|bool
     */
    public function getCacheMemoryUsed() {
        // Protection if Memcached not installed
        if (!class_exists('Memcached')) {
            return false;
        }

        try {
            $serversStats = Cache::store('memcached')->getMemcached()->getStats();
            $stats = reset($serversStats);
            $bytesUsed = $stats['bytes'];
            return $bytesUsed;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Returns memcached server uptime in seconds
     * @return int|bool
     */
    public function getCacheServerUptime() {
        // Protection if Memcached not installed
        if (!class_exists('Memcached')) {
            return false;
        }

        try {
            $serversStats = Cache::store('memcached')->getMemcached()->getStats();
            $stats = reset($serversStats);
            $uptime = $stats['uptime'];
            return $uptime;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $lat
     * @param $lon
     * @return string
     */
    private function findGeoAddressFromCache($lat, $lon) {
        // Protection if Memcached not installed
        if (!class_exists('Memcached')) {
            return '';
        }

        $cacheKey = $this->latLonToCacheKey($lat, $lon);
        // Memcached server may be down
        try {
            $server = Cache::store('memcached')->getMemcached();
            $value = $server->get($cacheKey);
            if (!$value) {
                return '';
            }
        } catch (\Exception $e) {
            return '';
        }
        return $value;
    }

    /**
     * Saves geo address to cache
     * @param $lat
     * @param $lon
     * @param $address
     * @return bool
     */
    private function saveGeoAddressToCache($lat, $lon, $address) {
        // Protection if Memcached not installed
        if (!class_exists('Memcached')) {
            return false;
        }

        $cacheKey = $this->latLonToCacheKey($lat, $lon);
        $cacheDays = (int)settings('main_settings.geocoder_cache_days'); // How long to keep cache

        $expiration = $cacheDays * 24 * 60 * 60;
        $maxExpiration = 60 * 60 * 24 * 30; // 30 days
        if ($expiration > $maxExpiration) { // memcached limitation
            $expiration = 0; // "unlimited" expiration
        }
        // Memcached server may be down
        try {
            $server = Cache::store('memcached')->getMemcached();
            $server->set($cacheKey, $address, $expiration);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
    
    /**
     * @param $lat
     * @param $lon
     * @return string
     */
    private function latLonToCacheKey($lat, $lon) {
        $key = $lat . $lon;

        return $key;
    }

    /**
     * Normalizes(rounds fraction part) of latitude|longitude
     * @param $value
     * @return int
     */
    private function normalizeGeoValue($value) {
        $rounded = round($value, 6);

        return $rounded;
    }

    /**
     * @param $lat
     * @param $lon
     * @param null $address
     * @return mixed|null|string
     */
    private function getGeoAddressFromApi($lat, $lon, $address = NULL) {
        $curl = new \Curl;
        $curl->follow_redirects = false;
        $curl->options['CURLOPT_SSL_VERIFYPEER'] = false;

        try {
        
            switch (settings('main_settings.geocoder_api')) {
                case 'default':
                    $data = $curl->get('http://test.com/app/gmaps/index.php', [
                        'lat' => $lat,
                        'lon' => $lon,
                        'format' => 'json',
                        'lang' => config('app.locale')
                    ]);
                    $arr = @json_decode($data->body, true);
                    if (is_array($arr) && array_key_exists('display_name', $arr) && !empty($arr['display_name']))
                        $address = $arr['display_name'];
                    break;
                case 'google':
                    $data = $curl->get('https://maps.googleapis.com/maps/api/geocode/json', [
                        'latlng' => $lat.','.$lon,
                        'key' => settings('main_settings.api_key')
                    ]);
                    $arr = @json_decode($data->body, TRUE);
                    if (is_array($arr)) {
                        if (array_key_exists('results', $arr) && !empty($arr['results'])) {
                            $address = current($arr['results'])['formatted_address'];
                        }
                        else {
                            if (array_key_exists('error_message', $arr))
                                return $arr['error_message'];
                        }
                    }
                    break;
                case 'geocodio':
                    $client = new Client(settings('main_settings.api_key'));
                    $address = $client->reverse($lat.','.$lon)->response;
                    if (isset($address->results['0']))
                        $address = $address->results['0']->formatted_address.', '.$address->results['0']->address_components->county;
                    else
                        $address = '';

                    break;
                case 'openstreet':
                    $data = $curl->get('https://nominatim.openstreetmap.org/reverse', [
                        'lat' => $lat,
                        'lon' => $lon,
                        'format' => 'json'
                    ]);

                    $arr = @json_decode($data->body, TRUE);
                    if (is_array($arr) && array_key_exists('display_name', $arr) && !empty($arr['display_name']))
                        $address = $arr['display_name'];
                    break;



                    // $ch = curl_init();
                    // $url = "https://nominatim.openstreetmap.org/reverse?lat={$lat}&lon={$lon}&format=json";
                    // curl_setopt($ch, CURLOPT_URL, $url);
                    // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
                    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    // $response = curl_exec($ch);
                    // if (curl_errno($ch)) {
                    //     dd(curl_error($ch));
                    // }
                    // curl_close($ch);

                    // dd($response);
                    // break;
                    
                    // $arr = @json_decode($response, true);
                    // $address = $arr;
                    // if (is_array($arr) && array_key_exists('display_name', $arr) && !empty($arr['display_name'])) {
                    //     $address = $arr['display_name'];
                    // }
                    // break;


                    // $ch = curl_init();
                    // $url = "https://nominatim.openstreetmap.org/reverse?lat={$lat}&lon={$lon}";
                    // // dd($url);
                    // curl_setopt($ch, CURLOPT_URL, $url);
                    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
                    // $response = curl_exec($ch);
                    // curl_close($ch);

                    // dd($response);
                case 'locationiq':
                    $data = $curl->get('http://locationiq.org/v1/reverse.php', [
                        'format' => 'json',
                        'key'    => settings('main_settings.api_key'),
                        'lat'    => $lat,
                        'lon'    => $lon,
                        'zoom'   => 18
                    ]);

                    $arr = @json_decode($data->body, TRUE);
                    if (is_array($arr) && array_key_exists('display_name', $arr) && !empty($arr['display_name']))
                        $address = $arr['display_name'];
                    break;
                case 'nominatim':
                    $data = $curl->get(settings('main_settings.api_url'), [
                        'lat' => $lat,
                        'lon' => $lon,
                        'format' => 'json',
                        'accept-language' => config('app.locale')
                    ]);
                    $arr = @json_decode($data->body, TRUE);
                    if (is_array($arr) && array_key_exists('display_name', $arr) && !empty($arr['display_name']))
                        $address = $arr['display_name'];

                    break;
            }
        } catch (\Exception $e) {
            return $e;
            // return "matin:";
        }
        return $address;
    }
}