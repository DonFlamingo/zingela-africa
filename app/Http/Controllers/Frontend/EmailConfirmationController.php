<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Facades\Repositories\UserRepo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Repositories\User\UserRepositoryInterface as User;
use Tobuli\Repositories\EmailTemplate\EmailTemplateRepositoryInterface as EmailTempalte;

class EmailConfirmationController extends Controller {
    /**
     * @var EmailTempalte
     */
    private $emailTemplate;

    function __construct(EmailTempalte $emailTemplate)
    {
        $this->emailTemplate = $emailTemplate;
    }

    public function edit() {
        $item = UserRepo::find(Auth::User()->id);

        return View::make('front::EmailConfirmation.edit')->with(compact('item'));
    }

    public function update() {
        $input = Input::all();
        $item = UserRepo::find(Auth::User()->id);
        try
        {
            if ($input['code'] != $item->email_activation)
                throw new ValidationException(['code' => trans('front.wrong_code')]);

            UserRepo::update($item->id, [
                'email_active' => 1,
                'email_activation' => ''
            ]);
            return Response::json(['status' => 1, 'id' => $item->id]);
        }
        catch (ValidationException $e)
        {
            return Response::json(['errors' => $e->getErrors()]);
        }
    }

    public function resendActivationCode() {
        return View::make('front::EmailConfirmation.resend');
    }

    public function resendActivationCodeSubmit() {
        $item = UserRepo::find(Auth::User()->id);
        try
        {
            if (strtotime($item->email_activation_sent) > (time() - 60))
                throw new ValidationException(['id' => trans('front.cant_resend_please_wait')]);

            $input['email_activation'] = rand(10000,99999);
            $input['email_activation_sent'] = date('Y-m-d H:i:s');
            $template = $this->emailTemplate->whereName('email_activation');
            sendEmailTemplate($template, $item->email, ['[code]' => $input['email_activation']], 'front::Emails.template', NULL, NULL, NULL, Config::get('app.locale'));

            UserRepo::update($item->id, $input);
            return Response::json(['status' => 1, 'id' => $item->id]);
        }
        catch (ValidationException $e)
        {
            return Response::json(['errors' => $e->getErrors()]);
        }
    }
}