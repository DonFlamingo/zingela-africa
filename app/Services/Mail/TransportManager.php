<?php

namespace App\Services\Mail;


use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Arr;
use Illuminate\Mail\TransportManager AS IlluminateTransportManager;

//use App\Services\Mail\PostmarkTransport;
//use App\Services\Mail\SendgridTransport;

class TransportManager extends IlluminateTransportManager
{
    /**
     * Create an instance of the Sendgrid Swift Transport driver.
     *
     * @return \Swift_SmtpTransport
     */
    protected function createSendgridDriver()
    {
        $config = $this->app['config']->get('services.sendgrid', []);

        $client = new HttpClient(Arr::get($config, 'guzzle', []));

        return new SendgridTransport($client, $config['secret']);
    }

    /**
     * Create an instance of the Postmark Swift Transport driver.
     *
     * @return \App\Services\Mail\PostmarkTransport
     */
    protected function createPostmarkDriver()
    {
        $config = $this->app['config']->get('services.postmark', []);

        $client = new HttpClient(Arr::get($config, 'guzzle', []));

        return new PostmarkTransport($client, $config['secret']);
    }
}
