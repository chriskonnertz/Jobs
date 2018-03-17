<?php

namespace ChrisKonnertz\Jobs\Cache;

use Cache;

/**
 * This class is a concrete implementation of the cache interface.
 * It is a wrapper around Laravel's Cache class (facade).
 */
class LaravelCache implements CacheInterface
{

    /**
     * {@inheritDoc}
     */
    public function has($key)
    {
        return Cache::has($key);
    }

    /**
     * {@inheritDoc}
     */
    public function forever($key, $value)    
    {
        Cache::forever($key, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        return Cache::get($key);
    }

    /**
     * {@inheritDoc}
     */
    public function forget($key)
    {
        Cache::forget($key);
    }

}
