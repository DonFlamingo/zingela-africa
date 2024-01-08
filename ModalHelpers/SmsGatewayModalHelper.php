<?php namespace ModalHelpers;

use Facades\Repositories\SmsEventQueueRepo;
use Facades\Validators\SendTestSmsFormValidator;
use Facades\Validators\SMSGatewayFormValidator;
use Tobuli\Exceptions\ValidationException;

class SmsGatewayModalHelper extends ModalHelper
{
    public function sendTestSms()
    {
        try {
            $this->validate($this->data);

            SendTestSmsFormValidator::validate('create', $this->data);

            $this->data['message'] = strtr($this->data['message'], [
                '<br>' => "\r\n",
                '&deg;' => '',
            ]);

            if ($this->data['request_method'] == 'app') {
                SmsEventQueueRepo::create([
                    'user_id' => $this->user->id,
                    'phone' => $this->data['mobile_phone'],
                    'message' => $this->data['message']
                ]);
            }
            else {
                $sms_gateway = [
                    'status' => 1,
                    'mobile_phone' => isset($_POST['mobile_phone']) ? $_POST['mobile_phone'] : $_GET['mobile_phone'],
                    'url' => $this->data['sms_gateway_url'],
                    'params' => [
                        'request_method' => $this->data['request_method'],
                        'authentication' => $this->data['authentication'],
                        'username' => $this->data['username'],
                        'password' => $this->data['password'],
                        'encoding' => $this->data['request_method'] == 'post' && $this->data['encoding'] == 'json' ? 'json' : null,
                        'auth_id' => $this->data['auth_id'],
                        'auth_token' => $this->data['auth_token'],
                        'senders_phone' => $this->data['senders_phone'],
                    ]
                ];

                $sms_template = new \stdClass();
                $sms_template->note = isset($_POST['message']) ? $_POST['message'] : $_GET['message'];

                send_sms_template($sms_template, $sms_gateway, $this->user->id, TRUE);
            }

            return ['status' => 1];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    public function clearQueue()
    {
        SmsEventQueueRepo::deletewhere(['user_id' => $this->user->id]);

        return ['status' => 1];
    }

    public function validate($data)
    {
        # SMS gateway POST and GET
        if ($data['request_method'] == 'post' || $data['request_method'] == 'get') {
            // Check if SMS gateway url contains http:// %NUMBER% %MESSAGE%
            if (isset($data['request_method']) && isset($data['sms_gateway']) && $data['sms_gateway']) {
                $sms_gateway_url = $data['sms_gateway_url'];
                if (strpos($sms_gateway_url, '%NUMBER%') === false || strpos($sms_gateway_url, '%MESSAGE%') === false) {
                    throw new ValidationException(['sms_gateway_url' => trans('front.sms_gateway_url_must_containt')]);
                }
            }

            if (isset($data['request_method']) && isset($data['authentication']) && $data['authentication']) {
                if (empty($data['username']))
                    throw new ValidationException(['username' => str_replace(':attribute', trans('validation.attributes.username'), trans('validation.required'))]);

                if (empty($data['password']) && !isset($this->user->sms_gateway_params['password']))
                    throw new ValidationException(['password' => str_replace(':attribute', trans('validation.attributes.password'), trans('validation.required'))]);
            }
        }
        # PLIVO
        elseif ($data['request_method'] == 'plivo') {
            SMSGatewayFormValidator::validate('plivo', $data);
        }
    }
}