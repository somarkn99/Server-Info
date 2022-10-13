<?php

namespace somarkn99\HostInfo\Providers;

use Illuminate\Support\ServiceProvider;
class HostInfoProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
