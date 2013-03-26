<?php

/**
 * Bioteaweb API
 *
 * A rest API frontend and indexer for the Biotea RDF project
 *
 * @link    http://biotea.idiginfo.org/api
 * @author  Casey McLaughlin <caseyamcl@gmail.com>
 * @license Copyright (c) Florida State University - All Rights Reserved
 */

// ------------------------------------------------------------------

namespace Bioteawebapi\Services\Indexer;
use Bioteawebapi\Entities\Document;
use Bioteawebapi\Entities\Annotation;
use Bioteawebapi\Entities\Term;
use Bioteawebapi\Entities\Topic;
use Bioteawebapi\Entities\Vocabulary;
use Bioteawebapi\Exceptions\IndexBuilderException;
use Bioteawebapi\Services\RDFFileClient;
use RecursiveDirectoryIterator as RDI;
use RecursiveIteratorIterator;
use Doctrine\ORM\EntityManager;
use SimpleXMLElement;

/**
 * Document builds non-database aware Indexable Entities from RDF files
 *
 * This class can also traverse folders and build from RDF files with a
 * specific path
 */
class IndexBuilder
{
    /**
     * @var Bioteawebapi\Services\RDFFileClient
     */
    private $files;

    /**
     * @var array
     */
    private $vocabularies = array();

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * Optionally accepts an array of vocabularies
     * (keys are shortnameses, values are URIs)
     *
     * @param Bioteawebapi\Services\RDFFileClient $files
     * @param array $vocabularies
     */
    public function __construct(RDFFileClient $files, Array $vocabularies = array())
    {
        //Set File client
        $this->files = $files;

        //Set vocabularies
        $this->setVocabularies($vocabularies);
    }

    // --------------------------------------------------------------

    /**
     * If indexing predefined vocabularies, set those here
     *
     * @param array  Keys are short names, values URIs
     */
    public function setVocabularies(Array $vocabularies)
    {
        $this->vocabularies = $vocabularies;
    }

    // --------------------------------------------------------------

    /**
     * Build the BioteaDocSet Object from an Annotation file, or pulls an
     * existing one out of the database
     *
     * @param string $relativePath    A relative file path to the file to parse
     * @return Bioteawebapi\Entities\Document
     */
    public function buildDocument($relativePath)
    {
        //Get full path
        $fullPath = $this->files->resolvePath($relativePath);
        $md5      = $this->files->getFilesMD5($relativePath);

        //Check file exists
        if ( ! is_readable($fullPath)) {
            throw new IndexBuilderException("The filepath does not exist: " . $fullPath);
        }

        //Build object
        $documentObj = new Document($relativePath, $md5);

        //Parse the main path
        $xml   = new SimpleXMLElement($fullPath, 0, true);
        $props = $this->parseMainFile($xml);
        $documentObj->setJournal($props['journal']);

        //If so, add them to the object
        foreach($this->files->getAnnotationFiles($relativePath) as $name => $relAnnotPath) {

            $fullAnnotPath = $this->files->resolvePath($relAnnotPath);

            //If file exists
            if (is_readable($fullAnnotPath)) {

                //Get the XML and build the Annotations
                $xml = new SimpleXMLElement($fullAnnotPath, 0, true);
                $annotations = $this->parseRdfAnnotationFile($xml);
                
                //Add the annotations and the path to the file the came from?
                $documentObj->addAnnotations($annotations);
                $documentObj->addAnnotationFilepath($name, $relAnnotPath);
            }
        }

        //Add a little information to new documentObj
        return $documentObj;
    }

    // --------------------------------------------------------------

    /**
     * Parse the RDF XML and get items that we want from it
     *
     * @param SimpleXMLElement $xml
     * @return array
     */
    protected function parseMainFile(SimpleXMLElement $xml)
    {
        $outItems = array();

        $xml->registerXPathNamespace('dcterms', 'http://purl.org/dc/terms/');
        $xml->registerXPathNamespace('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');        

        //Journal - Assume it is always the first dcterms:title item (could be dangerous...)
        $journal = $xml->xpath('//rdf:Description/dcterms:title[1]');
        if (count($journal) > 0) {
            $outItems['journal'] = (string) $journal[0];
        }

        return $outItems;
    }

    // --------------------------------------------------------------

    /**
     * Parse the RDF Annotation XML and build a document object graph
     *
     * @param \SimpleXMLElement $xml
     * @return array  Array of Entities\Annotation objects
     */
    protected function parseRdfAnnotationFile(SimpleXMLElement $xml)
    {
        //Setup an array
        $annotations = array();

        //Register namespaces
        $xml->registerXPathNamespace('ao', 'http://purl.org/ao/core/');
        $xml->registerXPathNamespace('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
        $xml->registerXPathNamespace('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');


        //Foreach annotation XML node, try to build an annotation
        foreach ($xml->xpath("//ao:Annotation") as $annot) {

            //Extract Term and build a Term object
            $_termVal = $annot->xpath('ao:body');
            $termString = (string) array_shift($_termVal);
            $termObj = $this->buildTermObj($termString);

            //Extract Topics
            foreach($annot->xpath('ao:hasTopic') as $topic) {

                //Attempt to get it from the hasTopic['rdf:resource'] attribute
                $topicUri = (string) $topic[0]->attributes('rdf', true)->resource;

                //If topicUri didn't work that way, then it is in the rdf:Description child node..
                if (empty($topicUri)) {

                    //Get the topic from the rdf:Description child
                    $desc = $topic[0]->children('rdf', true)->Description;
                    $topicUri = (string) $desc[0]->attributes('rdf', true)->about; 
                    $termObj->addTopic($this->buildTopicObj($topicUri));

                    //Also get the seeAlso's...
                    foreach($desc[0]->children('rdfs', true)->seeAlso as $seeAlso) {
                        $seeAlsoUri = (string) $seeAlso[0]->attributes('rdf', true)->resource;
                        $termObj->addTopic($this->buildTopicObj($seeAlsoUri));
                    }
                }
                else {
                    $termObj->addTopic($this->buildTopicObj($topicUri));
                }
            }

            //Build an annotation object and return it
            $annotations[] = new Annotation($termObj);
        }

        return $annotations;
    }

    // --------------------------------------------------------------

    /**
     * Build topic object from topic URI based on vocabularies
     *
     * Also builds vocabulary object and relates it to the topic if
     * possible
     *
     * @param string $topicUri
     * @return Topic Returns a topic object
     */
    protected function buildTopicObj($topicUri)
    {
        //Build the new topicObj
        $topicObj = new Topic($topicUri);

        //If vocbaulary not already set, attempt to set it
        if ( ! $topicObj->getVocabulary()) {

            $vocabObj = $this->buildVocabularyObj($topicUri);

            if ($vocabObj) {

                //Set the shortname
                $topicShortName = substr($topicUri, strlen($vocabObj->getUri()));
                $topicObj->setShortName($topicShortName);                

                //Set the vocabulary
                $topicObj->setVocabulary($vocabObj);
            }
        }

        return $topicObj;
    }

    // --------------------------------------------------------------

    /**
     * Build a term object
     *
     * @param string $term
     * @return Entities\Term
     */
    protected function buildTermObj($term)
    {
        $termObj = new Term($term);
        return $termObj;
    }

    // --------------------------------------------------------------

    /**
     * Build a vocabulary object
     *
     * Checks if existing vocabulary object exists in the database or builds
     * a new one.
     *
     * If vocabulary object cannot be built, returns null
     *
     * @param @string $uri  A topic URI
     * @return Entities\Vocabulary|null
     */
    protected function buildVocabularyObj($uri)
    {
        foreach($this->vocabularies as $shortName => $vocabUri) {        

            //Matching URI for Topic URI?
            if (
                strlen($uri) > strlen($vocabUri)
                && strcasecmp(substr($uri, 0, strlen($vocabUri)), $vocabUri) == 0
            ) {       
                //Build the vocabulary object
                return new Vocabulary($vocabUri, $shortName);
            }
        }

        //If made it here, no matching vocabulary was found
        return null;
    }    

}

/* EOF: BitoeaDocSetBuilder.php */