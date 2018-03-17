<?php

namespace ChrisKonnertz\Jobs\Integration;

use Illuminate\Support\ServiceProvider;
use ChrisKonnertz\Jobs\Cache\LaravelCache;

/**
 * This is a service provider class for Laravel
 */
class JobsServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind('jobs', function()
        {
            $cache = new LaravelCache;

            return new Jobs($cache);
        });
    }

}
