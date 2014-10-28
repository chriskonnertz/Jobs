<?php namespace ChrisKonnertz\Jobs;

interface CacheInterface {

    public function has($key);

    public function forever($key, $value);

    public function get($key);

    public function forget($key);

}