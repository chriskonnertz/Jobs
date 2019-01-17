<?php

namespace ChrisKonnertz\Jobs\Cache;

interface CacheInterface
{

    /**
     * Returns true if an item with the given key exists, false otherwise
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key) : bool;

    /**
     * Stores an item with a given key in the cache, without expiration
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function forever(string $key, $value);

    /**
     * Returns an item of the cache. May return null
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * Removes an item with a given key from the cache
     *
     * @param string $key
     * @return void
     */
    public function forget(string $key);

}
