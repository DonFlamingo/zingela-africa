<?php

namespace ModalHelpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

abstract class ModalHelper
{
    protected $user;
    protected $data;
    protected $api;


    public function __construct()
    {

        $this->api = boolval(Config::get('tobuli.api') == 1);
        $this->user = Auth::User();
        $this->data = request()->all();

        $nameParts = explode(' ', request()->name);

        $this->data['first_name']= $nameParts[0];
        $this->data['last_name']=isset($nameParts[1]) ? $nameParts[1] : '';

    }

    public function setData($data) {
        $this->data = $data;
    }

    public function setApi($bool) {
        $this->api = boolval($bool);
    }
}