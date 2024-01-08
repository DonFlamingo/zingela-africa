<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Netshell\Paypal\Facades\Paypal;
use Tobuli\Repositories\BillingPlan\BillingPlanRepositoryInterface as BillingPlan;
use Tobuli\Repositories\User\UserRepositoryInterface as User;

class PaymentsController extends Controller {

    private $_apiContext;

    public function __construct() {
        $this->_apiContext = PayPal::ApiContext(
            settings('main_settings.paypal_client_id'),
            settings('main_settings.paypal_secret'));

        $this->_apiContext->setConfig(array(
            'mode' => 'live',
            'service.EndPoint' => 'https://api.paypal.com',
            'http.ConnectionTimeOut' => 30,
            'log.LogEnabled' => true,
            'log.FileName' => storage_path('logs/paypal.log'),
            'log.LogLevel' => 'FINE'
        ));

    }

    public function getCheckout($id, BillingPlan $billingPlanRepo) {
        $plan = $billingPlanRepo->find($id);
        if (empty($plan))
            return redirect(route('subscriptions.renew'))->with(['message' => trans('front.plan_not_found')]);

        if (empty($plan->price))
            return redirect(route('payments.get_done', ['id' => Auth::User()->id, 'plan_id' => $plan->id]));

        $payer = PayPal::Payer();
        $payer->setPaymentMethod('paypal');

        $amount = PayPal::Amount();
        $amount->setCurrency(strtoupper(settings('main_settings.paypal_currency')));
        $amount->setTotal($plan->price);

        $transaction = PayPal::Transaction();
        $transaction->setAmount($amount);
        $transaction->setDescription(settings('main_settings.paypal_payment_name'));

        $redirectUrls = PayPal::RedirectUrls();
        $redirectUrls->setReturnUrl(route('payments.get_done', ['id' => Auth::User()->id, 'plan_id' => $plan->id]));
        $redirectUrls->setCancelUrl(route('payments.get_cancel'));

        $payment = PayPal::Payment();
        $payment->setIntent('sale');
        $payment->setPayer($payer);
        $payment->setRedirectUrls($redirectUrls);
        $payment->setTransactions(array($transaction));

        try {
            $response = $payment->create($this->_apiContext);
            $redirectUrl = $response->links[1]->href;
            return Redirect::to($redirectUrl);
        }
        catch(\Exception $e) {
            return redirect(route('subscriptions.renew'))->with(['message' => 'Unable to connect to paypal.']);
        }
    }

    public function getDone($user_id, $plan_id, Request $request, BillingPlan $billingPlanRepo, User $userRepo)
    {
        $id = $request->get('paymentId');
        $token = $request->get('token');
        $payer_id = $request->get('PayerID');

        $plan = $billingPlanRepo->find($plan_id);
        if (empty($plan))
            return redirect(route('subscriptions.renew'))->with(['message' => trans('front.plan_not_found')]);

        if (!empty($plan->price)) {
            try {
                $payment = PayPal::getById($id, $this->_apiContext);

                $paymentExecution = PayPal::PaymentExecution();

                $paymentExecution->setPayerId($payer_id);
                $executePayment = $payment->execute($paymentExecution, $this->_apiContext);
            } catch (\Exception $e) {
                return redirect(route('subscriptions.renew'))->with(['message' => trans('front.unexpected_error')]);
            }
        }

        if (Auth::check())
            $user = Auth::User();
        else
            $user = $userRepo->find($id);

        if (strtotime($user->subscription_expiration) > time() && $user->billing_plan_id == $plan->id)
            $date = date('Y-m-d H:i:s', strtotime($user->subscription_expiration." + {$plan->duration_value} {$plan->duration_type}"));
        else
            $date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')." + {$plan->duration_value} {$plan->duration_type}"));

        $update = [
            'billing_plan_id' => $plan->id,
            'devices_limit' => $plan->objects,
            'subscription_expiration' => $date
        ];

        $userRepo->update($user->id, $update);


        return redirect(route('subscriptions.renew'))->with(['success' => trans('front.payment_received')]);
    }

    public function getCancel() {
        // Curse and humiliate the user for cancelling this most sacred payment (yours)
        return Redirect::route('home');
    }
}
