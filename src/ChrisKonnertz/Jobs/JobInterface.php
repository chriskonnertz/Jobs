<?php namespace ChrisKonnertz\Jobs;

interface JobInterface {

    public function getName();

    public function getActive();

    public function getInterval();

    public function run($executed);   

}