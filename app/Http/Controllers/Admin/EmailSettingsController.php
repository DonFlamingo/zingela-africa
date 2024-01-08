<?php namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config as LaravelConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Tobuli\Repositories\Config\ConfigRepositoryInterface as Config;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Validation\AdminEmailSettingsFormValidator;
use Swift_SmtpTransport as SmtpTransport;

class EmailSettingsController extends BaseController {
    /**
     * @var AdminEmailSettingsFormValidator
     */
    private $adminEmailSettingsFormValidator;
    /**
     * @var Config
     */
    private $config;

    function __construct(AdminEmailSettingsFormValidator $adminEmailSettingsFormValidator, Config $config) {
        parent::__construct();
        $this->adminEmailSettingsFormValidator = $adminEmailSettingsFormValidator;
        $this->config = $config;
    }

    public function index() {
		$settings = unserialize(DB::table('configs')->where('title', 'email')->first()->value);
        if (isset($settings['signature'])) {
            $settings['signature'] = str_replace("<br>", "\r", $settings['signature']);
        }

        $providers = [
            'smtp' => trans('front.default'),
            'sendgrid' => 'SendGrid',
            'postmark' => 'Postmark',
            'mailgun' => 'Mailgun'
        ];

        return View::make('admin::EmailSettings.index')->with(compact('settings', 'providers'));
    }

    public function save() {
        $input = Input::all();
        $item = DB::table('configs')->where('title', 'email')->first();
        $item_arr = unserialize($item->value);
        if (isset($item_arr['smtp_password']) && (!isset($input['smtp_password']) || empty($input['smtp_password'])))
            $input['smtp_password'] = $item_arr['smtp_password'];

        if (!isset($input['use_smtp_server']) || ($input['use_smtp_server'] != 0 && $input['use_smtp_server'] != 1))
            return Redirect::route('admin.email_settings.index')->withInput();

        try
        {
            if ($_ENV['server'] == 'demo')
                throw new ValidationException(['id' => trans('front.demo_acc')]);
            
            if ($input['provider'] == 'smtp') {
                $this->adminEmailSettingsFormValidator->validate('use_smtp_server_' . $input['use_smtp_server'], $input);

                if ($input['use_smtp_server'] == 1) {
                    $update = [
                        'from_name' => $input['from_name'],
                        'noreply_email' => $input['noreply_email'],
                        'use_smtp_server' => $input['use_smtp_server'],
                        'smtp_server_host' => $input['smtp_server_host'],
                        'smtp_server_port' => $input['smtp_server_port'],
                        'smtp_security' => $input['smtp_security'],
                        'smtp_username' => $input['smtp_username'],
                        'smtp_password' => $input['smtp_password'],
                    ];
                }
                else {
                    $update = [
                        'from_name' => $input['from_name'],
                        'noreply_email' => $input['noreply_email'],
                    ];
                }
            }
            elseif ($input['provider'] == 'mailgun') {
                $this->adminEmailSettingsFormValidator->validate('mailgun', $input);
                $update = [
                    'from_name' => $input['from_name'],
                    'noreply_email' => $input['noreply_email'],
                    'api_key' => $input['api_key'],
                    'domain' => $input['domain'],
                ];
            }
            else {
                $this->adminEmailSettingsFormValidator->validate('sendgrid', $input);
                $update = [
                    'from_name' => $input['from_name'],
                    'noreply_email' => $input['noreply_email'],
                    'api_key' => $input['api_key'],
                ];
            }

            $update['provider'] = $input['provider'];
            $update['signature'] = str_replace(["\r\n", "\r", "\n"], "<br>", $input['signature']);

            $this->config->update($item->id, [
                'value' => serialize($update)
            ]);

            return Redirect::route('admin.email_settings.index')->withSuccess(trans('front.successfully_saved'));
        }
        catch (ValidationException $e)
        {
            return Redirect::route('admin.email_settings.index')->withInput()->withErrors($e->getErrors());
        }
    }

    public function testEmail() {
        return View::make('admin::EmailSettings.test_email');
    }

    public function testEmailSend() {
        $input = Input::all();

        try {
            $body = view('front::Emails.template')->with(array('body' => 'Test', 'lang' => Auth::User()->lang))->render();

            $res = \Facades\MailHelper::send($input['email'], $body, 'Test email', 'en', FALSE);

            if (!$res['status'])
                throw new ValidationException(['id' => $res['error']]);
        }
        catch (ValidationException $e)
        {
            return Response::json(['status' => 0, 'errors' => $e->getErrors()]);
        }

        Session::flash('success', trans('front.successfully_saved'));

        return Response::json(['status' => 1, 'trigger' => 'window_reload']);
    }
}
