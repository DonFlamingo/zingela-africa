<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Tobuli\Helpers\Settings;

class SettingsServiceProvider extends ServiceProvider {

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Tobuli\Helpers\Settings', function ($app) {
            return new Settings();
        });
    }

}