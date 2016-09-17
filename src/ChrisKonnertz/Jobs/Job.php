<?php namespace ChrisKonnertz\Jobs;

abstract class Job implements JobInterface {

    /**
     * The (unique) name of the job
     * @var string
     */
    protected $name = '';

    /**
     * If true the jobs is going to be executed.
     * Set to false to pause execution.
     * @var boolean
     */
    protected $active = true;

    /**
     * The cool down time (minutes).
     * If it is less than the job scheduler's
     * cool down time it is ignored.
     * @var integer
     */
    protected $interval = 1;

    /**
     * Returns the name of the job.
     * 
     * @return boolean
     */
    public function getName() {
        return $this->name;
    }
   
    /**
     * Returns true if the job is active.
     * 
     * @return boolean
     */
    public function getActive() {
        return $this->active;
    }
   
    /**
     * Returns the cool down time
     * 
     * @return integer
     */
    public function getInterval() {
        return $this->interval;
    }

    /**
     * Runs the job.
     * 
     * @return void
     */
    abstract public function run($executed);

}
