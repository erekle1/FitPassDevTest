<?php

namespace App\Providers;

use App\Services\PasswordGenerator\PasswordGenerator;
use Illuminate\Support\ServiceProvider;

class PasswordGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('PasswordGenerator', function () {
            return new PasswordGenerator(config('passwordgenerator.default_length'), config('passwordgenerator.default_strength'));
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
