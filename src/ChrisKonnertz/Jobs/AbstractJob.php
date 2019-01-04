<?php

namespace ChrisKonnertz\Jobs;

/**
 * This is the abstract base class for all concrete Job classes.
 * The concrete Job class has to implemnent its run() method.
 * It might also overwrite the default value of the $interval property.
 */
abstract class AbstractJob implements JobInterface
{

    /**
     * If true the jobs is going to be executed.
     * Set to false to pause execution.
     *
     * @var bool
     */
    protected $active = true;

    /**
     * The cool down time (minutes).
     * If it is less than the job scheduler's
     * cool down time it is ignored.
     *
     * @var integer
     */
    protected $interval = 1;

    /**
     * Returns the unique name of the job.
     * The name is used as an identifier.
     * 
     * @return string
     */
    public function getName()
    {
        // Use the name of the concrete Job class as name
        return get_class($this);
    }
   
    /**
     * Returns true if the job is active.
     * 
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }
   
    /**
     * Returns the cool down time of the job
     * 
     * @return int
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * Runs the job. Implement this method in the concrete job class.
     *
     * @param int|null $executedAt Time of the last execution of this job
     * @return void
     */
    abstract public function run($executedAt);

}
