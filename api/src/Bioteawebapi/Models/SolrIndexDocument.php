<?php

namespace Bioteawebapi\Models;

/**
 * SolrIndex Document Entity
 */
class SolrIndexDocument
{  
    /**
     * Calculated MD5 of the filepath
     */
    private $id;

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

    /**
     * Constructor
     *
     * Conforms to Solarium requirement that the constructor accept
     * a parameter (array) representing the class proprties
     *
     * @param array $params 
     */
    public function __construct($params = array())
    {
        //Set the parameters per Solarium requirements
        foreach($params as $param => $value)
        {
            $method = 'set' . ucfirst($param);
            call_user_func(array($this, $method), $value);
        }
    }

    // --------------------------------------------------------------

    public function __get($prop)
    {
        return $this->$prop;
    }

    // --------------------------------------------------------------

    public function params()
    {
        return get_object_vars($this);
    }

    // --------------------------------------------------------------

    public function setRdfFilePath($path)
    {
        $this->rdfFilePath = $path;
        $this->id = md5($path);
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