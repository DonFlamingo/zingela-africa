<?php

namespace Tobuli\Helpers;

use Illuminate\Support\Facades\DB;

class Settings {

    private $cache;

    public function get($key) {
        return $this->merge($key);
    }

    public function set($key, $value) {
        $this->cache = [];

        return $this->setDB($key, $value);
    }

    public function has($key) {
        return ! is_null( $this->get($key) );
    }

    public function merge($key) {
        if ( isset($this->cache[$key]) ) {
            return $this->cache[$key];
        }

        // from config
        $cfg_value = $this->getConfig($key);

        // from db
        $db_value = $this->getDB($key);

        if (!is_null($db_value))
        {
            if (is_array($db_value))
            {
                $cfg_value = $cfg_value ? $cfg_value : [];
                $value = array_merge_recursive_distinct($cfg_value, $db_value);
            } else
            {
                $value = $db_value;
            }
        } else
        {
            $value = $cfg_value;
        }

        $this->cache[$key] = $value;

        return $value;
    }

    private function getDB($key) {
        if (empty($key))
            return null;

        $keys = explode('.', $key);

        $group = array_shift($keys);

        if (empty($group))
            return null;

        try {
            $item = DB::table('configs')->where('title', '=', $group)->first();
        }
        catch (\Exception $e) {
            $item = null;
        }

        if (empty($item))
            return null;

        try {
            $value = $this->get_array_value( unserialize($item->value), $keys );
        }
        catch (\Exception $e) {
            $value = $item->value;
        }

        return $value;
    }

    private function setDB($key, $value) {
        if (empty($key))
            return false;

        $keys = explode('.', $key);

        $group = array_shift($keys);

        if (empty($group))
            return false;

        $item = DB::table('configs')->where('title', '=', $group)->first();

        if (empty($item))
            DB::table('configs')->insert(['title' => $group, 'value' => '']);


        try {
            $group_value = unserialize($item->value);
        }
        catch (\Exception $e) {}

        if (empty($group_value))
            $group_value = [];


        $this->set_array_value( $group_value, $keys, $value );

        if ( is_array($group_value) ) {
            $value = serialize( $group_value );
        }

        return DB::table('configs')->where('title', '=', $group)->update(['value' => $value]);
    }

    private function getConfig($key) {
        return config('tobuli.' . $key);
    }

    private function get_array_value($array, $keys) {
        if (empty($keys))
            return $array;

        $key = array_shift($keys);

        if (isset($array[$key]))
            return $this->get_array_value( $array[$key], $keys );
        else
            return null;
    }

    private function set_array_value(&$array, $keys, $value) {
        if (empty($keys))
            return $array = $value;

        $key = array_shift($keys);

        if (!isset($array[$key]))
            $array[$key] = null;

        return $this->set_array_value( $array[$key], $keys, $value );
    }
}