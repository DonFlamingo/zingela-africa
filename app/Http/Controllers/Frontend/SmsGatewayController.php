<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Facades\ModalHelpers\SmsGatewayModalHelper;

class SmsGatewayController extends Controller
{

    public function testSms()
    {
        return view('front::SmsGateway.test_sms');
    }

    public function sendTestSms()
    {
        return SmsGatewayModalHelper::sendTestSms();
    }

    public function clearQueue()
    {
        return SmsGatewayModalHelper::clearQueue();
    }
}