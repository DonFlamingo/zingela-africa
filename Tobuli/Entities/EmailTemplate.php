<?php namespace Tobuli\Entities;

use Eloquent;

class EmailTemplate extends Eloquent {
	protected $table = 'email_templates';

    protected $fillable = array('title', 'note');

    public $timestamps = false;

}
