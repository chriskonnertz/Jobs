<?php 

namespace ChrisKonnertz\Jobs;

/**
 * All concrete job classes have to implement this interface.
 * They might also inherit from the AbstractJob class (which also implements this interface).
 */
interface JobInterface
{

    /**
     * Returns the unique name of the job. The name is used as an identifier.
     * The method might simply return the name of the concrete job class.
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
     * Returns the cool down time in minutes
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
