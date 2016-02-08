<?php

namespace Uppdragshuset\AO\Repository;

use Illuminate\Support\ServiceProvider;
use App;

class TenantServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands('Uppdragshuset\AO\Repository\Commands\MakeRepository');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
