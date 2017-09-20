<?php namespace ChrisKonnertz\Jobs;

interface CacheInterface {

    /**
     * Returns true if an item with the given ke exists
     *
     * @param string $key
     * @return bool
     */
    public function has($key);

    /**
     * Stores an item with a given key in the cache without expiration
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function forever($key, $value);

    /**
     * Returns an item of the cache. May return null
     *
     * @param string $key
     * @return mixed
     */
    public function get($key);

    /**
     * Removes an item with a given key from the cache
     *
     * @param string $key
     * @return void
     */
    public function forget($key);

}