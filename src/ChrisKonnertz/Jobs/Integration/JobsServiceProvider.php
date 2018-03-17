<?php

namespace ChrisKonnertz\Jobs\Integration;

use ChrisKonnertz\Jobs\Cache\LaravelCache;
use ChrisKonnertz\Jobs\Jobs;
use Illuminate\Support\ServiceProvider;

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
