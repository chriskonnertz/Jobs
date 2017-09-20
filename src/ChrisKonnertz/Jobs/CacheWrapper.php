<?php

namespace ChrisKonnertz\Jobs;

use Cache;

class CacheWrapper implements CacheInterface
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