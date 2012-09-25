<?php

class FiniteTaskTracker extends Tracker
{
	private $totalCount;

	private $estTimeRemains;

	public function __construct($totalNum)
	{
		$this->totalCount = $totalNum;

		parent::__construct();
	}
}

/* EOF: finiteTaskTracker.php */