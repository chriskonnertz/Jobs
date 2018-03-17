<?php

namespace ChrisKonnertz\Jobs\Integration;

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
            $cache = new CacheWrapper;

            return new Jobs($cache);
        });
    }

}
