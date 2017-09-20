<?php 

namespace ChrisKonnertz\Jobs;

interface JobInterface
{

    /**
     * Returns the name of the job.
     * 
     * @return boolean
     */
    public function getName();
   
    /**
     * Returns true if the job is active, false if it is paused (=not executed)
     * 
     * @return boolean
     */
    public function getActive();
   
    /**
     * Returns the cool down time
     * 
     * @return integer
     */
    public function getInterval();

    /**
     * This method is called when the job is run. Overwrite it in your Job class.
     *
     * @param int|null $executed Time of last execution
     * @return void
     */
    public function run($executed);   

}
