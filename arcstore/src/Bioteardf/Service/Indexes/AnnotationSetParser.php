<?php

namespace Bioteardf\Service\Indexes;

use Bioteardf\Exception\BioteaRdfParseException;
use Bioteardf\Model\Doc;
use SimpleXMLElement;

/**
 *
 */
class AnnotationSetParser extends RdfFileParser
{
    /**
     * @var array
     */
    private $vocabList;

    // --------------------------------------------------------------

    /**
     * Constructor
     * 
     * @param array $vocabList  List of vocabularies for which to record topics
     */
    public function __construct(array $vocabList = array())
    {
        $this->setVocabList($vocabList);
    }

    // --------------------------------------------------------------

    /**
     * Set the vocabulary list
     *
     * @param array  Key/value vocabulary list [shorname => full URI, etc.]
     */
    public function setVocabList(array $vocabList)
    {
        $this->vocabList = $vocabList;
    }

    // --------------------------------------------------------------

    public function parse(SimpleXMLElement $xml, Doc\Document $doc)
    {
        //Go through each annotation entity
        foreach ($xml->xpath('ao:Annotation') as $annot) {
            $this->extractAnnotation($annot, $xml, $doc);
        }

        //Also go through the aot:ExactQualifier entities
        foreach ($xml->xpath('aot:ExactQualifier') as $annot) {
            $this->extractAnnotation($annot, $xml, $doc);
        }
    }

    // --------------------------------------------------------------

    private function extractAnnotation(SimpleXMLElement $annotXml, SimpleXMLElement $mainXml, Doc\Document $doc)
    {
        //Get the annotation and the term
        $annotId   = (string) $annotXml->attributes('rdf', true)->about;
        $annotTerm = (string) current($annotXml->xpath('ao:body'));

        //Build objects
        $annotObj = new Doc\Annotation($annotId);
        $termObj  = new Doc\Term($annotTerm);

        //Get the context instances
        foreach($annotXml->xpath('ao:context') as $context) {
            $this->extractContextInformation($context, $annotObj, $termObj, $mainXml, $doc);
        }    

        //Add topic/vocabulary information
        foreach ($annotXml->xpath('ao:hasTopic') as $topicXml) {
            $this->extractTopicInformation($topicXml, $termObj);
        }
    }

    // --------------------------------------------------------------

    private function extractContextInformation(SimpleXMLElement $contextXml, Doc\Annotation $annotObj, Doc\Term $termObj, SimpleXMLElement $mainXml, Doc\Document $doc)
    {
        //Start and end characters for this context default to null
        $startChar = null;
        $endChar   = null;

        //If there is the attribute rdf:nodeID, then this annotation refers to another annotation's context identifier 
        if ($contextXml->attributes('rdf', true)->nodeID) {
            $nodeID      = (string) $contextXml->attributes('rdf', true)->nodeID;
            $paragraphId = current($mainXml->xpath("//ao:context/aold:ElementSelector[@rdf:nodeID='{$nodeID}']/rdfs:resource"))->attributes('rdf', true)->resource;
        }
        else { //it is a normal annotation

            //Get the selector
            $selector = $contextXml->xpath('aold:StartEndElementSelector');

            //Start and end character ids exist
            if (is_object(current($selector))) {
                $selector = current($selector);

                $startChar = (string) current($selector->xpath('aos:init'));
                $endChar   = (string) current($selector->xpath('aos:end'));
            }
            else { //We're using the wrong node; use the right one
                $selector = $contextXml->xpath('aold:ElementSelector');
                $selector = current($selector);
            }

            //Get the paragraph ID
            $paragraphId = (string) current($selector->xpath('rdfs:resource'))->attributes('rdf', true)->resource;
        }

        //Get the paragraph object for the given id
        foreach($doc->paragraphs as $pgrh) {
            if (strcasecmp($paragraphId, $pgrh->identifier) == 0) {
                $paragraphObj = $pgrh;
            }
        }

        //if no paragraph object, sanity check time.
        if ( ! isset($paragraphObj)) {
            throw new BioteaRdfParseException("Could not resolve paragraph for " . $paragraphId);
        }

        $instance = new Doc\TermInstance($paragraphObj, $annotObj, $startChar, $endChar);
        $annotObj->addTermInstance($instance);
    }

    // --------------------------------------------------------------

    private function extractTopicInformation(SimpleXMLElement $topicXml, Doc\Term $termObj)
    {
        $topicUris = array();

        //Try getting the topicUri from 
        $topicUri = $topicXml->attributes('rdf', true)->resource;

        if ( ! is_null($topicUri)) {
            $topicUris[] = (string) $topicUri;
        }
        else {

            $topicUris[] = (string) current($topicXml->xpath('rdf:Description'))->attributes('rdf', true)->about;
            foreach($topicXml->xpath('rdf:Description/rdfs:seeAlso') as $seeAlso) {
                $topicUris[] = (string) $seeAlso->attributes('rdf', true)->resource;                
            }
        }

        foreach($topicUris as $topicUri) {
            $vocabUri = $this->deriveVocabForTopic($topicUri);

            if ($vocabUri) {
                $vocabObj = new Doc\Vocabulary($vocabUri, array_search($vocabUri, $this->vocabList));
                $topicObj = new Doc\Topic($topicUri, $vocabObj);
                $termObj->addTopic($topicObj);
            }
        }
    }

    // --------------------------------------------------------------

    /**
     * Derive Vocabulary URI from topic URI
     *
     * @var    string          $topicUri
     * @return string|boolean  Vocabulary URI, or false if not on the list of vocabs
     */
    private function deriveVocabForTopic($topicUri)
    {
        foreach($this->vocabList as $shortName => $vocabUri) {

            if (strcasecmp($vocabUri, substr($topicUri, 0, strlen($vocabUri))) == 0) {
                return $vocabUri;
            }
        }

        //If Made it here, return false for no voabulary found
        return false;
    }      
}

/* EOF: AnnotationSetParser.php */