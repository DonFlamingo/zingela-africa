<?php namespace Tobuli\Entities;

use Eloquent;

class Route extends Eloquent {
	protected $table = 'routes';

    protected $fillable = array('user_id', 'name', 'active', 'color');

}
