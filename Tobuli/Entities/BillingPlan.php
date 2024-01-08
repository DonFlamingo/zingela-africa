<?php namespace Tobuli\Entities;

use Eloquent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class BillingPlan extends Eloquent {
	protected $table = 'billing_plans';

    protected $fillable = [
        'title',
        'price',
        'objects',
        'duration_type',
        'duration_value'
    ];

    public $timestamps = false;
    private $permissions = NULL;

    public function perm($name, $mode) {
        $mode = trim($mode);
        $modes = Config::get('tobuli.permissions_modes');

        if (!array_key_exists($mode, $modes))
            die('Bad permission');

        if (is_null($this->permissions)) {
            $this->permissions = [];
            $perms = DB::table('billing_plan_permissions')
                ->select('name', 'view', 'edit', 'remove')
                ->where('plan_id', '=', $this->id)
                ->get();

            if (!empty($perms)) {
                foreach ($perms as $perm) {
                    $this->permissions[$perm->name] = [
                        'view' => $perm->view,
                        'edit' => $perm->edit,
                        'remove' => $perm->remove
                    ];
                }
            }
        }

        return array_key_exists($name, $this->permissions) && array_key_exists($mode, $this->permissions[$name]) ? boolval($this->permissions[$name][$mode]) : FALSE;
    }
}
