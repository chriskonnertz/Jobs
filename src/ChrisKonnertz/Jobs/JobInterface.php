<?php 

namespace ChrisKonnertz\Jobs;

interface JobInterface
{

    /**
     * Returns the name of the job. It might simply return the name of the concrete job class.
     * 
     * @return string
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
     * This method is called when the job is run. Overwrite it in the concrete Job class.
     *
     * @param int|null $executedAt Time of last execution
     * @return void
     */
    public function run($executedAt);   

}
