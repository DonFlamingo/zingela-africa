<?php namespace Tobuli\Entities;

use Eloquent;

class UserGprsTemplate extends Eloquent {
	protected $table = 'user_gprs_templates';

    protected $fillable = array(
        'user_id',
        'title',
        'message'
    );
}
