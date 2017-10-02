<?php

namespace ChrisKonnertz\Jobs;

abstract class Job implements JobInterface
{

    /**
     * The (unique) name of the job
     *
     * @var string
     */
    protected $name = '';

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
     * Returns the name of the job.
     * 
     * @return bool
     */
    public function getName()
    {
        return $this->name;
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
     * Returns the cool down time
     * 
     * @return int
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * Runs the job. Overwrite this method in the concrete job class.
     *
     * @param int|null $executed Time of last execution
     * @return void
     */
    abstract public function run($executed);

}
