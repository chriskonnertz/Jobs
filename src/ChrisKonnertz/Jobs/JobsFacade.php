<?php namespace ChrisKonnertz\Jobs;

use Illuminate\Support\Facades\Facade;

class JobsFacade extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'jobs'; }

}