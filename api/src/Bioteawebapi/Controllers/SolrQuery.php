<?php

namespace Bioteawebapi\Controllers;
use Bioteawebapi\Services\SolrClient;
use Silex\Application;

class SolrQuery extends Controller
{
	private $solrClient;

	// --------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @param Bioteawebapi\Services\SolrClient $solrClient
	 */
	public function __construct(Application $app, SolrClient $solrClient)
	{
		parent::__construct($app);
		
		$this->solrClient = $solrClient;
	}

	// --------------------------------------------------------------

	public function run($field, $value = 'null')
	{
		return "$field -- $value";
	}

	// --------------------------------------------------------------

	public function getAllowedFormats()
	{
		//Maybe add more later
		return array('json');
	}
}

/* EOF: SolrQuery.php */