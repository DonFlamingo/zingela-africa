<?php namespace App\Providers;

use Illuminate\Mail\MailServiceProvider;
use App\Services\Mail\TransportManager;
use Facades\Settings;

class ConfigurableMailServiceProvider extends MailServiceProvider {

    /**
     * Register the Swift Transport instance.
     *
     * @return void
     */
    protected function registerSwiftTransport()
    {
        $this->loadConfig();

        $this->app['swift.transport'] = $this->app->share(function ($app) {
            return new TransportManager($app);
        });
    }

    protected function loadConfig()
    {
        //$config = $this->app['config']->get('mail');

        $settings = Settings::get('email');

        $config = [
            'driver' => isset($settings['provider']) ? $settings['provider'] : 'mail',
            'host'   => isset($settings['smtp_server_host']) ? $settings['smtp_server_host'] : '',
            'port'   => isset($settings['smtp_server_port']) ? $settings['smtp_server_port'] : '',
            'from'   => [
                'address' => isset($settings['noreply_email']) ? $settings['noreply_email'] : env('email_from'),
                'name'    => isset($settings['from_name']) ? $settings['from_name'] : env('email_name')
            ],
            'encryption' => empty($settings['smtp_security']) ? '' : $settings['smtp_security'],
            'username'   => isset($settings['smtp_username']) ? $settings['smtp_username'] : '',
            'password'   => isset($settings['smtp_password']) ? $settings['smtp_password'] : '',
        ];

        if ( $config['driver'] == 'smtp' && empty($settings['use_smtp_server']) )
            $config['driver'] = 'mail';

        switch ($config['driver']) {
            case 'sendgrid':
                $this->app['config']->set('services.sendgrid', [
                    'secret' => isset($settings['api_key']) ? $settings['api_key'] : ''
                ]);
                break;
            case 'postmark':
                $this->app['config']->set('services.postmark', [
                    'secret' => isset($settings['api_key']) ? $settings['api_key'] : ''
                ]);
                break;
            case 'mailgun':
                $this->app['config']->set('services.mailgun', [
                    'secret' => isset($settings['api_key']) ? $settings['api_key'] : '',
                    'domain' => isset($settings['domain']) ? $settings['domain'] : '',
                ]);
                break;
        }

        $this->app['config']->set('mail', $config);
    }

}