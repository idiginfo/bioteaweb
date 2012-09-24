<?php

namespace Bioteawebapi\Models;

class SolrIndexDocument
{
	/**
	 * @var string  Path to RDF file relative to RDF docs root
	 */
	private $rdfFilePath;

	/**
	 * @var array  Paths to RDF annotation files realtive to docs root
	 */
	private $rdfAnnotationFilePaths = array();

	/**
	 * @var array  Array of terms
	 */
	private $terms = array();

	/**
	 * @var array  Array of topics
	 */
	private $topics = array();

	/**
	 * @var array  Array of vocabularies
	 */
	private $vocabularies = array();

	// --------------------------------------------------------------

	public function __get($val)
	{
		return $this->$val;
	}

	// --------------------------------------------------------------

	public function setRdfFilePath($path)
	{
		$this->rdfFilePath = $path;
	}

	// --------------------------------------------------------------

	public function setRdfAnnotationFilePaths(Array $paths)
	{
		$this->rdfAnnotationFilePaths = $paths;
	}

	// --------------------------------------------------------------

	public function appendRdfAnnotationFilePath($path)
	{
		$this->rdfAnnotationFilePaths[] = $path;
	}

	// --------------------------------------------------------------

	public function setTerms(Array $terms)
	{
		$this->terms = $terms;
	}

	// --------------------------------------------------------------

	public function addTerm($term)
	{
		$this->terms[] = $term;
	}

	// --------------------------------------------------------------

	public function setVocabularies(Array $vocabularies)
	{
		$this->vocabularies = $vocabularies;
	}

	// --------------------------------------------------------------

	public function addVocabulary($vocabulary)
	{
		$this->vocabularies[] = $vocabulary;
	}

	// --------------------------------------------------------------

	public function setTopics(Array $topics)
	{
		$this->topics = $topics;
	}

	// --------------------------------------------------------------

	public function addTopic($topic)
	{
		$this->topic[] = $topic;
	}	

}

/* EOF: SolrIndexDocument.php */