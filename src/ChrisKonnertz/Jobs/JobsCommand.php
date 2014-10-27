<?php namespace ChrisKonnertz\Jobs;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Cache;

class JobsCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'jobs';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Execute those jobs of the pool that do not need a cool down';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$counter = JobsFacade::run();

		if ($counter === false) {
			$this->error('Job executor needs a cool down! No jobs executed.');
		} else {
			$this->info('Done. Jobs executed: '.$counter.'/'.JobsFacade::count());	
		}		
	}

}
