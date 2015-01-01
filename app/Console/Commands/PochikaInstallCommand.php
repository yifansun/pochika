<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Conf;

class PochikaInstallCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'pochika:install';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Install Pochika';

	protected $source_path;

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		try {
			$this->info('Pochika Installing...');

			// create config.yml
			$this->copyConfigFile();

			$this->source_path = Conf::get('source', 'source');

			// create source dir
			if (!file_exists($this->source_path)) {
				mkdir($this->source_path);
				$this->line('created: '.$this->source_path);
			} else {
				$this->line('exists: '.$this->source_path);
			}

			// create posts/pages/themes dir
			$this->copyResource('posts');
			$this->copyResource('pages');
			$this->copyResource('themes');

			$this->info('Pochika is successfully installed.');
		} catch (\ErrorException $e) {
			$this->error("error: ".$e->getMessage());
		}
	}

	public function copyConfigFile()
	{
		if (file_exists(base_path('config.yml'))) {
			$this->line('exists: config.yml');
			return;
		}

		$src = base_path('/resources/blog/config.yml');
		$dst = base_path('/config.yml');
		if (!copy($src, $dst)) {
			throw new \LogicException('cannot copy config.yml');
		}
		$this->line('created: config.yml');
	}

	protected function copyResource($name)
	{
		$src_path = base_path().'/resources/blog/'.$name;
		$dst_path = $this->source_path.'/'.$name;

		if (!file_exists($dst_path)) {
			if (is_dir($src_path)) {
				copy_r($src_path, $dst_path);
			} else {
				copy($src_path, $dst_path);
			}
			$this->line('created: '.$dst_path);
		} else {
			$this->line('exists: '.$dst_path);
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			//['example', InputArgument::REQUIRED, 'An example argument.'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			//['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

}
