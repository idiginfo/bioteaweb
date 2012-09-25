<?php

/**
 * A class for keeping track of long running jobs
 */
class TaskTracker
{
	const self::ERROR = 3;
	const self::WARN = 2;
	const self::SUCCESS = 1;

	private $successCount;

	private $errorCount;

	private $warnCount;

	private $timeElapsed;

	private $currMemUsage;

	private $maxMemUsage;

	private $avgSingleTickTime;

	private $maxSingleTickTime;

	private $minSingleTickTime;

	public function tick($ct = 1, $status = self::SUCCESS);

	public function start();

	public function abort($msg);

	public function finish($msg);


}

/* EOF: Tracker.php */