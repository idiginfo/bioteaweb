<?php

namespace Bioteawebapi\Services;
use Bioteawebapi\Services\SOlrIndexDocumentManager as DocumentManager;

/**
 * Solr Indexer indexes RDF documents into SOLR
 */
class SolrIndexer
{
	/**
	 * @var string  Path to use
	 */
	private $path;

	/**
	 * @var DocumentManager
	 */
	private $solrMgr;

	/**
	 * @var ???
	 */
	private $rdfParser;

	// --------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @param string  $path  Path to RDF files
	 * @param Indexer $solrMgr
	 * @param ???     $rdfParser
	 */
	public function __construct($path, DocumentManager $solrMgr, $rdfParser)
	{
		if ( ! is_readable($path) OR ! is_dir($path)) {
			throw new \InvalidArgumentException("The RDF file path is invalid: " . $path);
		}
	}

	// --------------------------------------------------------------

	
}


/* EOF: SolrIndexer.php */