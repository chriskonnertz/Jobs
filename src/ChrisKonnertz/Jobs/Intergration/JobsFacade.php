<?php

namespace ChrisKonnertz\Jobs\Integration;

use Illuminate\Support\Facades\Facade;

/**
 * This is a facade class for Laravel
 */
class JobsFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { 
        return 'jobs'; 
    }

}
