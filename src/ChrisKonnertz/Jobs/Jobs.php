<?php namespace ChrisKonnertz\Jobs;

use ReflectionClass;
use Closure;

class Jobs {

    /**
     * The cache object
     * @var Cache
     */
    protected $cache;

    /**
     * Array of job objects
     * @var array
     */
    protected $jobs = array();

    /**
     * They key namespace used for caching job names
     * @var string
     */
    protected $cacheKey = 'jobs.';

    /**
     * The cool down time span (seconds) 
     * @var integer
     */
    protected $timeSpan = 1;

    /**
     * Constructor.
     * $cache has to implement these methods:
     * has, get, forever, forget
     * 
     * @param object $cache
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
    public function getChacheKey()
    {
        return $this->cacheKey;
    }

    /**
     * Sets the cache key
     * 
     * @param  string $cacheKey
     * @return void
     */
    public function cacheKey($cacheKey)
    {
        if (! $cacheKey) {
            throw new JobException('The cache key can not be empty.');
        }

        $this->cacheKey = $cacheKey;
    }

    /**
     * Returns the cool down time span
     * 
     * @return int
     */
    public function getTimeSpan()
    {
        return $this->timeSpan;
    }

    /**
     * Sets the cool down time span
     * 
     * @param  int $timeSpan
     * @return void
     */
    public function timeSpan($timeSpan)
    {
        if (! is_numeric($timeSpan)) {
            throw new JobException('The time span has to be numeric.');
        }

        if ($timeSpan < 1) {
            throw new JobException('The time span must not be less than 1.');
        }

        $this->timeSpan = $timeSpan;
    }

    /**
     * Returns true if a job with that name exists
     * 
     * @param  string  $name The name of the job
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->jobs[$name]);
    }

    /**
     * Returns the job with the given name
     * 
     * @param  string $name The name of the job
     * @return Job
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->getOrMake($name);
        }

        throw new JobException("There is no such job: '$name'");
    }

    /**
     * Adds a jobs to the pool
     * 
     * @param Job $job
     * @return void
     */
    public function add(Job $job)
    {
        if (! $job->getName()) {
            throw new JobException('Add a name to the job.');
        }

        $jobs[$job->getName()] = $job;
    }

    /**
     * Adds only the a "constructor". With the help of this constructor
     * the job will be created as late as possible.
     * $constructor is either a class name or a closure that creates 
     * and returns a Job instance.
     * 
     * @param string        $Name           The name of the job
     * @param constructor   string|Closure  Class name or closure
     * @return void
     */
    public function addLazy($name, $constructor)
    {
        if (! $name) {
            throw new JobException('Set the name of the job.');
        }

        if (! $constructor)
        {
            throw new JobException('Set the constructor for the job.');
        }

        if (! is_a($constructor, 'Closure') and ! is_string($constructor)) {
            throw new JobException('The constructor can be a class name or a closure.');
        }

        $this->jobs[$name] = $constructor;
    }

    /**
     * Removes a job from the pool.
     * 
     * @param  Job    $job
     * @return bool
     */
    public function remove($name)
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
    public function count()
    {
        return sizeof($this->jobs);
    }

    /**
     * Returns an array with all jobs
     * 
     * @return array
     */
    public function all()
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

        if ($this->cache->has($this->cacheKey)) {
            $executed = $this->cache->get($this->cacheKey);

            if ($now - $executed < $this->timeSpan) {
                return false;
            }
        }

        $this->cache->forever($this->cacheKey, $now);

        $counter = 0;
        foreach ($this->jobs as $name => $jobBag) {
            $job = $this->getOrMake($name);

            $key = $this->makeCacheKey($name);

            $executed = null;
            if ($this->cache->has($key)) {
                $executed = $this->cache->get($key);

                if ($now - $executed < $job->getTimeSpan()) {
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
    protected function makeCacheKey($name)
    {
        return $key = $this->cacheKey.$name;
    }

    /**
     * If a job with the given name exists,
     * this method returns the job.
     * If there is no such job yet,
     * it will create, store and return it.
     * 
     * @param  string $name
     * @return Job
     */
    protected function getOrMake($name)
    {
        $value = $this->jobs[$name];

        if (is_a($value, 'Job')) {
            return $value;
        }

        if (is_string($value)) {
            $rc = new ReflectionClass($value);

            $job = $rc->newInstance(); // Create instance
        } else {
            $job = $value(); // Execute closure
        }

        if (! is_a($job, 'Job')) {
            throw new JobException("Object '$name' is not a job!");
        }

        $this->jobs[$name] = $job;

        return $job;
    }

}