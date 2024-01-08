<?php namespace Tobuli\Entities;

use Eloquent;

class SmsTemplate extends Eloquent {
	protected $table = 'sms_templates';

    protected $fillable = array('title', 'note');

    public $timestamps = false;

}
