<?php namespace ChrisKonnertz\Jobs;

use ChrisKonnertz\Jobs\Jobs;
use Illuminate\Support\ServiceProvider;

class JobsServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->bind('jobs', function()
        {
            $cache = $this->app['cache'];

            return new Jobs($cache);
        });
    }

}