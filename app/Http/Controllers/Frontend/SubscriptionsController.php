<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Tobuli\Repositories\BillingPlan\BillingPlanRepositoryInterface as BillingPlan;

class SubscriptionsController extends Controller {

    public function index() {
        $dStart = new DateTime(date('Y-m-d H:i:s'));
        $dEnd  = new DateTime(Auth::User()->subscription_expiration);
        $dDiff = $dStart->diff($dEnd);
        $days_left = $dDiff->days;

        return View::make('front::Subscriptions.index')->with(compact('item', 'days_left'));
    }

    public function pricing() {
        $email = NULL;
        if (Auth::User()->id != 0)
            $email = '?email='.base64_encode(Auth::User()->email);
        return View::make('front::Subscriptions.pricing')->with(compact('email'));
    }

    public function languages() {
        $languages = [
            'ar' => 'Arabic',
            'au' => 'Australian',
            'az' => 'Azerbaijan',
            'br' => 'Brazilian',
            'ch' => 'Chile',
            'nl' => 'Dutch',
            'dk' => 'Danish',
            'uk' => 'English(UK)',
            'en' => 'English(USA)',
            'fr' => 'French',
            'fi' => 'Finnish',
            'de' => 'German',
            'it' => 'Italian',
            'ph' => 'Philippines',
            'pl' => 'Polish',
            'pt' => 'Portuguese',
            'ro' => 'Romanian',
            'sr' => 'Serbian',
            'es' => 'Spanish',
            'sk' => 'Slovakian',
            'sv' => 'Swedish',
            'th' => 'Thai',
        ];

        $soon = [
            'no' => 'Norwegian',
            'tr' => 'Turkish',
        ];

        return View::make('front::Subscriptions.languages', compact('languages', 'soon'));
    }

    public function renew(BillingPlan $billingPlanRepo) {
        if (settings('main_settings.enable_plans') != 1)
            return Redirect::route('home');

        $permissions = Config::get('tobuli.permissions');

        $plans = $billingPlanRepo->getWhere([], 'objects', 'asc');

        return view('front::Subscriptions.renew')->with(compact('plans', 'permissions'));
    }
}