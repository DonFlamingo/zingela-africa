<?php namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Tobuli\Repositories\Config\ConfigRepositoryInterface as Config;
use Tobuli\Validation\AdminBackupsFormValidator;
use Tobuli\Exceptions\ValidationException;

class BackupsController extends BaseController {
    /**
     * @var Array
     */
    private $periods;
    /**
     * @var Array
     */
    private $hours;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var AdminBackupsFormValidator
     */
    private $adminBackupsFormValidator;

    function __construct(Config $config, AdminBackupsFormValidator $adminBackupsFormValidator) {
        parent::__construct();
        $this->config = $config;
        $this->adminBackupsFormValidator = $adminBackupsFormValidator;


        $this->periods = [
            '1' => '1 '.trans('front.day'),
            '3' => '3 '.trans('front.days'),
            '7' => '7 '.trans('front.days'),
            '30' => '30 '.trans('front.days'),
        ];

        $this->hours = [
            '00:00' => '00:00',
            '01:00' => '01:00',
            '02:00' => '02:00',
            '03:00' => '03:00',
            '04:00' => '04:00',
            '05:00' => '05:00',
            '06:00' => '06:00',
            '07:00' => '07:00',
            '08:00' => '08:00',
            '09:00' => '09:00',
            '10:00' => '10:00',
            '11:00' => '11:00',
            '12:00' => '12:00',
            '13:00' => '13:00',
            '14:00' => '14:00',
            '15:00' => '15:00',
            '16:00' => '16:00',
            '17:00' => '17:00',
            '18:00' => '18:00',
            '19:00' => '19:00',
            '20:00' => '20:00',
            '21:00' => '21:00',
            '22:00' => '22:00',
            '23:00' => '23:00',
        ];
    }

    public function index() {
        $settings = [];
        $item = $this->config->findWhere(['title' => 'backups']);
        if (empty($item))
            $this->config->create(['title' => 'backups', 'value' => serialize([])]);
        else
            $settings = unserialize($item->value);

        $periods = $this->periods;

        $hours = $this->hours;

        return View::make('admin::Backups.index')->with(compact('settings', 'periods', 'hours'));
    }

    public function panel() {
        $settings = [];
        $item = $this->config->findWhere(['title' => 'backups']);
        if (empty($item))
            $this->config->create(['title' => 'backups', 'value' => serialize([])]);
        else
            $settings = unserialize($item->value);

        $periods = $this->periods;

        $hours = $this->hours;

        return View::make('admin::Backups.panel')->with(compact('settings', 'periods', 'hours'));
    }

    public function save() {
        $input = Input::all();
        $item = $this->config->findWhere(['title' => 'backups']);
        $settings = unserialize($item->value);
        try
        {
            if ($_ENV['server'] == 'demo')
                throw new ValidationException(['id' => trans('front.demo_acc')]);

            $this->adminBackupsFormValidator->validate('update', $input);

            if (empty($input['ftp_password']) && empty($settings['ftp_password']))
                throw new ValidationException(['id' => strtr(trans('validation.required'), [':attribute' => trans('validation.attributes.ftp_password')])]);

            if (empty($input['ftp_password']))
                $input['ftp_password'] = $settings['ftp_password'];

            beginTransaction();
            try {
                if (!isset($settings['next_backup']) || $settings['period'] != $input['period'] || $settings['hour'] != $input['hour'])
                    $settings['next_backup'] = strtotime(date('Y-m-d', strtotime('+'.$input['period'].' days')).' '.$input['hour']);

                $settings['ftp_server'] = $input['ftp_server'];
                $settings['ftp_port'] = $input['ftp_port'];
                $settings['ftp_username'] = $input['ftp_username'];
                $settings['ftp_password'] = $input['ftp_password'];
                $settings['ftp_path'] = $input['ftp_path'];
                $settings['period'] = $input['period'];
                $settings['hour'] = $input['hour'];
                $this->config->update($item->id, [
                    'value' => serialize($settings)
                ]);
            }
            catch (\Exception $e) {
                rollbackTransaction();
                throw new ValidationException(['id' => trans('global.unexpected_db_error')]);
            }

            commitTransaction();

            try {
                $conn_id = ftp_connect($input['ftp_server'], $input['ftp_port']);
                if (!$login_result = @ftp_login($conn_id, $input['ftp_username'], $input['ftp_password'])) {
                    throw new \Exception(trans('front.login_failed'));
                }
                ftp_close($conn_id);
            }
            catch(\Exception $e) {
                throw new ValidationException(['id' => $e->getMessage()]);
            }

            return Redirect::route('admin.backups.index')->withSuccess(trans('front.successfully_saved'));
        }
        catch (ValidationException $e)
        {
            return Redirect::route('admin.backups.index')->withInput()->withErrors($e->getErrors());
        }
    }

    public function test() {
        $item = $this->config->findWhere(['title' => 'backups']);
        if (empty($item))
            return Response::json(['status' => trans('front.unexpected_error')]);

        $settings = unserialize($item->value);
        if (!isset($settings['ftp_server']))
            return Response::json(['status' => trans('front.unexpected_error')]);

        $test_file = 'test_ftp_backup.txt';
        $test_file_path = '/var/www/html/images/test_ftp_backup.txt';
        @file_put_contents($test_file_path, 'Test FTP backup');
        $message = trans('front.successfully_uploaded');
        $status = 1;

        try {
            $conn_id = ftp_connect($settings['ftp_server'], $settings['ftp_port']);
            if (!$login_result = @ftp_login($conn_id, $settings['ftp_username'], $settings['ftp_password'])) {
                throw new \Exception(trans('front.login_failed'));
            }
            ftp_chdir($conn_id, $settings['ftp_path']);
            ftp_put($conn_id, $test_file, $test_file_path, FTP_ASCII);
            @ftp_delete($conn_id, $test_file);
            ftp_close($conn_id);
        }
        catch(\Exception $e) {
            $message = explode(':', $e->getMessage(), 2);
            $message = isset($message['1']) ? trim($message['1']) : trans('front.unexpected_error');
            $status = 0;
        }

        return Response::json(['status' => $status, 'message' => $message]);
    }

    public function logs() {
        $settings = [];
        $item = $this->config->findWhere(['title' => 'backups']);
        if (empty($item))
            $this->config->create(['title' => 'backups', 'value' => serialize([])]);
        else
            $settings = unserialize($item->value);

        return View::make('admin::Backups.logs')->with(compact('settings'));
    }
}
