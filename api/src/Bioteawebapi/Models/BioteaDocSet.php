<?php

namespace Bioteawebapi\Models;
use SimpleXMLElement;

/**
 * Represents a Biotea Document Set (main file + annotations)
 */
class BioteaDocSet
{
    /**
     * @param string
     */
    private $mainFilePath;

    /**
     * @var array  Array of filenames  (annotationName)
     */
    private $annotationFileNames = array();

    /**
     * @var array  Keys are term strings, values are term objects
     */
    private $terms = array();

    /**
     * @var array  Keys are topic URIs, values are topic objects
     */
    private $topics = array();

    /**
     * @var array  Keys are shortnames, values are full URIs
     */
    private $vocabularies = array();

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * Currently does nothing with the main RDf graph, but it is there
     * for future use
     * 
     * @param string  $filepath      Relative to document basepath
     * @param array   $vocabularies  An array of all avialable vocabularies
     */
    public function __construct($filepath, Array $vocabularies = array())
    {
        //Set path
        $this->mainFilePath = $filepath;
     
        //Sort the vocabularies based on strlen
        uasort($vocabularies, function($a, $b) {
            return strlen($b) - strlen($a);
        });

        //Set optional vocabularies
        $this->vocabularies = $vocabularies;
    }

    // --------------------------------------------------------------

    /**
     * Add Annotation File
     * 
     * @param \EasyRdf_Graph $mainfile
     * @param string         $annotationName    Name for the Annotation file
     * @param string         $filepath          Relative to document basepath
     */
    public function addAnnotationFile(SimpleXMLElement $xml, $annotationName, $filepath)
    { 
        assert(is_string($filepath));
        $this->annotationFileNames[$annotationName] = $filepath;
        $this->extractItems($xml, $annotationName);
    }

    // --------------------------------------------------------------

    /**
     * Extract terms and topics from the RDF XML
     *
     * @param \EasyRdf_Graph $rdf
     */
    protected function extractItems(SimpleXMLElement $xml)
    {
        //Register namespaces
        $xml->registerXPathNamespace('ao', 'http://purl.org/ao/core/');
        $xml->registerXPathNamespace('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
        $xml->registerXPathNamespace('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');

        // Terms are at XPATH  //ao:annotation//ao:body
        // Topics are at XPATH //ao:annotation//ao:hasTopic//rdf:description
        //               and   //ao:annotation//ao:hasTopic//rdfs:seeAlso

        foreach ($xml->xpath("//ao:Annotation") as $annot) {

            //Extract Term
            $term = (string) array_shift($annot->xpath('ao:body'));

            //Extract Topics
            $topics = array();
            foreach($annot->xpath('ao:hasTopic') as $topic) {
                $topicUri = (string) $topic[0]->attributes('rdf', true)->resource;

                if (empty($topicUri)) {
                    $desc = $topic[0]->children('rdf', true)->Description;
                    $topics[] = (string) $desc[0]->attributes('rdf', true)->about; 
                    foreach($desc[0]->children('rdfs', true)->seeAlso as $seeAlso) {
                        $topics[] = (string) $seeAlso[0]->attributes('rdf', true)->resource;
                    }
                }
                else {
                    $topics[] = $topicUri;
                }
            }

            //Build the term
            $this->terms[$term] = new BioteaTerm($term);

            //Build the topics arrays
            foreach($topics as $topic) {
                $this->topics[$topic] = $this->buildTopicObj($topic);
                $this->terms[$term]->addTopic($this->topics[$topic]);
            }
        }
    }

    // --------------------------------------------------------------

    /**
     * Get the terms
     *
     * @return array
     */
    public function getTerms()
    {
        return array_values($this->terms);
    }

    // --------------------------------------------------------------

    /**
     * Get the topics
     *
     * @return array
     */
    public function getTopics()
    {
        return array_values($this->topics);
    }

    // --------------------------------------------------------------

    /**
     * Get the main filepath
     *
     * @return string
     */
    public function getMainFilePath()
    {
        return $this->mainFilePath;
    }

    // --------------------------------------------------------------

    /**
     * Get the annotation file paths as incremental array
     *
     * @return array
     */
    public function getAnnotationFilePaths()
    {
        return array_values($this->annotationFileNames);
    }

    // --------------------------------------------------------------

    /**
     * Build topic object from topic URI based on vocabularies
     *
     * @param string $topicUri
     * @return BioteaTopic      Returns a topic object
     */
    protected function buildTopicObj($topicUri)
    {
        $topicObj = new BioteaTopic($topicUri);

        //Attempt to determine which vocabulary is in use
        foreach($this->vocabularies as $shortName => $uri)
        {
            if (
                strlen($topicUri) > strlen($uri)
                && strcasecmp(substr($topicUri, 0, strlen($uri)), $uri) == 0
            ) {
                
                $topicObj->setVocabularyUri($uri);
                $topicObj->setVocabularyShortName($shortName);

                //break up the string to get the topicShortname
                $topicShortName = substr($topicUri, strlen($uri));
                $topicObj->setTopicShortName($topicShortName); 

                break;
            }
        }

        return $topicObj;
    }
}

/* EOF: BioteaRdfDocSet */