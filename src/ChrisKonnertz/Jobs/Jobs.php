<?php

namespace ChrisKonnertz\Jobs;

use ReflectionClass;
use Closure;
use ChrisKonnertz\Jobs\Cache\CacheInterface;

class Jobs
{

    /**
     * The current version of this library
     */
    const version = '3.2.2';

    /**
     * The cache object
     * @var CacheInterface
     */
    protected $cache;

    /**
     * Array of job objects
     * @var JobInterface[]
     */
    protected $jobs = array();

    /**
     * They key namespace used for caching job names
     * @var string
     */
    protected $cacheKey = 'jobs.';

    /**
     * The minimum cool down time (minutes) for all jobs
     * @var integer
     */
    protected $coolDown = 1;

    /**
     * Constructor.
     * The cache object has to implement these methods: has(), get(), forever(), forget()
     * 
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Returns the cache key
     * 
     * @return string
     */
    public function getCacheKey() : string
    {
        return $this->cacheKey;
    }

    /**
     * Sets the cache key
     * 
     * @param  string $cacheKey
     * @return void
     */
    public function cacheKey(string $cacheKey)
    {
        if ($cacheKey == '') {
            throw new JobException('The cache key can not be empty.');
        }
        
        $this->cacheKey = $cacheKey;
    }

    /**
     * Returns the minimum cool down time (minutes) for all jobs
     * 
     * @return int
     */
    public function getCoolDown() : int
    {
        return $this->coolDown;
    }

    /**
     * Sets the minimum cool down time (minutes) for all jobs
     * 
     * @param  int $coolDown The cool down time in minutes
     * @return void
     */
    public function coolDown(int $coolDown)
    {
        if ($coolDown < 1) {
            throw new JobException('The cool down time must not be less than 1.');
        }

        $this->coolDown = $coolDown;
    }

    /**
     * Returns true if a job with that name exists
     * 
     * @param  string  $name The name of the job
     * @return bool
     */
    public function has(string $name) : bool
    {
        return isset($this->jobs[$name]);
    }

    /**
     * Returns the job with the given name
     * 
     * @param  string $name The name of the job
     * @return JobInterface
     */
    public function get(string $name) : JobInterface
    {
        if ($this->has($name)) {
            return $this->getOrMake($name);
        }

        throw new JobException("There is no such job: '$name'");
    }

    /**
     * Adds a job to the pool
     * 
     * @param JobInterface $job
     * @return void
     */
    public function add(JobInterface $job)
    {
        if (! $job->getName()) {
            throw new JobException('Add a name to the job.');
        }

        $jobs[$job->getName()] = $job;
    }

    /**
     * Adds only a "builder". With the help of this builder
     * the job will be created as late as possible.
     * $builder is either a class name or a closure that creates 
     * and returns a Job instance.
     * 
     * @param string         $name    The name of the job
     * @param string|Closure $builder Class name or closure
     * @return void
     */
    public function addLazy(string $name, $builder)
    {
        if (! $name) {
            throw new JobException('Set the name of the job.');
        }
        if (! $builder) {
            throw new JobException('Set the builder for the job.');
        }
        if (! is_a($builder, 'Closure') and ! is_string($builder)) {
            throw new JobException('The builder can only be a class name or a closure.');
        }

        $this->jobs[$name] = $builder;
    }

    /**
     * Removes a job from the pool
     * 
     * @param  string $name
     * @return bool
     */
    public function remove(string $name) : bool
    {
        $key = $this->makeCacheKey($name);
        
        $this->cache->forget($key);
        
        unset($this->jobs[$name]);
    }

    /**
     * Removes all jobs from the pool
     * 
     * @return void
     */
    public function clear()
    {
        foreach ($this->jobs as $name => $job) {
            $key = $this->makeCacheKey($name);
            $this->cache->forget($key);
        }

        $this->jobs = array();
    }

    /**
     * Counts the jobs in the pool
     * 
     * @return int
     */
    public function count() : int
    {
        return sizeof($this->jobs);
    }

    /**
     * Returns an array with all jobs
     * 
     * @return JobInterface[]
     */
    public function all() : array
    {
        $jobs = array();

        foreach ($this->jobs as $name => $jobBag) {
            $jobs[$name] = $this->getOrMake($name);
        }

        return $jobs;
    }

    /**
     * Runs all jobs that do not have a cool down.
     * Returns false or the number of executed jobs.
     * 
     * @return boolean|int
     */
    public function run()
    {
        $now = time();

        if ($this->remainingCoolDown() > 0) {
            return false;
        }

        $this->cache->forever($this->cacheKey, $now);

        $counter = 0;
        foreach ($this->jobs as $name => $jobBag) {
            $job = $this->getOrMake($name);

            $key = $this->makeCacheKey($name);

            $executed = null;
            if ($this->cache->has($key)) {
                $executed = $this->cache->get($key);

                if ($now - $executed < $job->getInterval() * 60) {
                    continue;
                }
            }

            if ($job->getActive()) {
                $now = time();

                $job->run($executed);
                $this->cache->forever($key, $now);
                $counter++;
            }
        }

        return $counter;
    }

    /**
     * Returns the number of minutes the job executor still is in cool down mode.
     * Minimum is 0.
     *
     * @return int
     */
    public function remainingCoolDown() : int
    {
        $now = time();

        if ($this->cache->has($this->cacheKey)) {
            $executedAt = $this->cache->get($this->cacheKey);

            $remainingCoolDown = $executedAt + $this->coolDown * 60 - $now;

            return max($remainingCoolDown / 60, 0);
        }

        return 0;
    }

    /**
     * Returns the timestamp (in seconds) of the last time the run() method has been called.
     * Returns null if this did not happen so far.
     * Note: If the cache is reset, this timestamp will be reset as well!
     *
     * @return int|null
     */
    public function lastRunAt()
    {
        if ($this->cache->has($this->cacheKey)) {
            $executedAt = $this->cache->get($this->cacheKey);
            
            return $executedAt;
        } else {
            return null;
        }
    }

    public function __toString()
    {
        $string = '[';

        foreach ($this->jobs as $name => $job) {
            if (strlen($string > 1)) {
                $string .= ', ';
            }

            $string .= $name;
        }

        return $string.']';
    }

    /**
     * Makes a cache key for a job
     * 
     * @param  string $name The name of the job
     * @return string
     */
    protected function makeCacheKey(string $name) : string
    {
        return $key = $this->cacheKey.$name;
    }

    /**
     * If a job with the given name exists, this method returns the job.
     * If there is no such job yet, it will create, store and return it.
     * 
     * @param  string $name
     * @return JobInterface
     */
    protected function getOrMake(string $name) : JobInterface
    {
        $value = $this->jobs[$name];

        if ($value instanceof JobInterface) {
            return $value;
        }

        if (is_string($value)) {
            $reflectionClass = new ReflectionClass($value);

            $job = $reflectionClass->newInstance(); // Create instance
        } else {
            /** @var Closure $value */
            $job = $value(); // Execute closure
        }

        if (! $job instanceof JobInterface) {
            throw new JobException("Object '$name' is not a job!");
        }

        $this->jobs[$name] = $job;

        return $job;
    }

}
