<?php

class JobsTest extends PHPUnit_Framework_TestCase
{
    
    protected function getInstance()
    {
        return new ChrisKonnertz\Jobs\Jobs();
    }

    protected function getDummy()
    {
        $jobs = $this->getInstance();

        return $jobs;
    }

    public function testSomething()
    {
        $jobs = $this->getDummy();

        //$this->assertTrue($jobs->has('jobname'));
        
    }

}