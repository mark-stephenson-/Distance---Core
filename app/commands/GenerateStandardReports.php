<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateStandardReports extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'core:generate-standard-reports';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate questionnaire reports in chunks of 20 per ward';

	/**
	 * Create a new command instance.
	 *
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
		$reportService = new \Core\Services\ReportService();

		$this->info('Started to generate standard reports');

		$reportService->generateAllStandardReports(20);

		$this->info('Finished to generate standard reports');
	}


}
