<?php namespace ChrisKonnertz\Jobs;

use Cache;

class CacheWrapper implements CacheInterface {

    public function has($key)
    {
        return Cache::has($key);
    }

    public function forever($key, $value)    
    {
        Cache::forever($key, $value);
    }

    public function get($key)
    {
        return Cache::get($key);
    }

    public function forget($key)
    {
        Cache::forget($key);
    }

}