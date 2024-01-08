<?php namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Tobuli\Repositories\Config\ConfigRepositoryInterface as Config;
use Tobuli\Validation\AdminDatabaseClearFormValidator;
use Tobuli\Exceptions\ValidationException;

class DatabaseClearController extends BaseController {
    /**
     * @var Config
     */
    private $config;
    /**
     * @var AdminDatabaseClearFormValidator
     */
    private $adminBackupsFormValidator;

    function __construct(Config $config, AdminDatabaseClearFormValidator $adminDatabaseClearFormValidator) {
        parent::__construct();
        $this->config = $config;
        $this->adminDatabaseClearFormValidator = $adminDatabaseClearFormValidator;
    }

    public function panel() {
        $settings = [];
        $item = $this->config->findWhere(['title' => 'db_clear']);
        if (empty($item))
            $this->config->create(['title' => 'db_clear', 'value' => serialize([])]);
        else
            $settings = unserialize($item->value);

        $size = getDatabaseSize(['tracking_sensors','tracking_traccar','tracking_web','tracking_engine_hours']);
        $size = formatBytes( $size );

        return View::make('admin::DatabaseClear.panel')->with(compact('settings', 'size'));
    }

    public function save() {
        $input = Input::all();
        $item = $this->config->findWhere(['title' => 'db_clear']);
        $settings = unserialize($item->value);

        try
        {
            $this->adminDatabaseClearFormValidator->validate('update', $input);

            $settings['status'] = empty($input['status']) ? 0 : $input['status'];
            $settings['days']   = empty($input['days']) ? 90 : $input['days'];

            $this->config->update($item->id, [
                'value' => serialize($settings)
            ]);

            return Redirect::route('admin.tools.index')->withSuccess(trans('front.successfully_saved'));
        }
        catch (ValidationException $e)
        {
            return Redirect::route('admin.tools.index')->withInput()->withErrors($e->getErrors());
        }
    }
}
