<?php namespace ChrisKonnertz\Jobs;

interface JobInterface {

    public function getName();

    public function getActive();

    public function getTimeSpan();

    public function run($executed);   

}