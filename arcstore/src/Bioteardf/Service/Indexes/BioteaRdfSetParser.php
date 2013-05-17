<?php

namespace Bioteardf\Service\Indexes;

use Bioteardf\Exception\BioteaRdfParseException;
use Bioteardf\Model\BioteaRdfSet;
use Bioteardf\Model\Doc;
use SimpleXMLElement, Exception;

/**
 * Parses Biotea RDF Sets into a graph of Doc Objects
 */
class BioteaRdfSetParser
{
    /**
     * @var array
     */
    private $vocabList;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param array   $vocabList  Key/value vocabulary list [shortname => full URI, etc.]
     */
    public function __construct(array $vocabList)
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

    /**
     * Analyze a set and return its terms/topics/vocabularies set
     *
     * @param  Bioteardf\Model\BioteaRdfSet $rdfSet  RDF Set to analyze
     * @param  boolean                      $strict  If TRUE, will throw exception on annotation parse error
     * @return Bioteardf\Model\Doc\Document
     */
    public function analyzeSet(BioteaRdfSet $rdfSet, $strict = false)
    {
        //Derive PMID from filename
        $pmid = substr($rdfSet->mainFile->getBaseName('.' . $rdfSet->mainFile->getExtension()), 3);

        //Build a DocObj
        $docObj = new Doc\Document($rdfSet->md5, $pmid);

        //Setup the main Document
        try {
            $mainXml = @new SimpleXMLElement($rdfSet->mainFile, 0, true);
            $this->extractFromMainDocFile($mainXml, $docObj);
        }
        catch (Excpetion $e) {
            throw new BioteaRdfParseException("Could not read main file: " . (string) $rdfSet->mainFile);            
        }


        //Parse Annotation File
        // foreach($rdfSet->annotationFiles as $annotFile) {

        //     try {
        //         $annotXml = @new SimpleXMLElement($annotFile, 0, true);
        //         $this->extractFromAnnotsXML($annotXml, $ttv);
        //     }
        //     catch (Exception $e) {

        //         if ($strict) {
        //             throw new BioteaRdfParseException("Could not read annotation file: " . (string) $annotFile);
        //         }
        //     }
        // }

        return $docObj;
    }


    // --------------------------------------------------------------

    public function extractFromMainDocFile(SimpleXMLElement $xml, Doc\Document $doc)
    {
        //Find the journal name (first journal listed)
        $journalName = $this->xpath($xml, '//rdf:Description//rdf:type[@rdf:resource = "http://purl.org/ontology/bibo/Journal"][1]/following-sibling::dcterms:publisher');

        //LEFT OFF HERE LEFT OFF HERE .. Need to get all the paragraphs
        $xpath = '//rdf:Description//rdf:type[@rdf:resource="http://www.w3.org/2011/content#Content"]/parent::rdf:Description';
        //Do the xpath, and for each result, derive the attribute for the rdf:Description to get the paragraph id
        //Then the child entity cnt:chars is the value (don't forget to run utf8_encode() on it)

    }

    // --------------------------------------------------------------

    /**
     * Extract terms, topics, and vocabs from annotation XML
     *
     * @param  SimpleXMLElement                  $xml
     * @param  Bioteardf\Model\TermsTopicsVocabs $ttv
     * @return Bioteardf\Model\TermsTopicsVocabs
     */
    public function extractFromAnnotsXML(SimpleXMLElement $xml)
    {
        //Assumptions:
        // - term is always derived from topic
        // - topic always has a vocabulary

        $xml = $this->setupNs($xml);

        //Foreach annotation XML node, try to build an annotation
        foreach ($xml->xpath("//ao:Annotation") as $annot) {
            $ttv = $this->analyzeAnnotation($annot, $ttv);
        }

        return $ttv;
    }

    // --------------------------------------------------------------

    /**
     * Analyze a single Annotation
     *
     * @param  SimpleXMLElement                  $annot
     * @param  Bioteardf\Model\TermsTopicsVocabs $ttv
     */
    public function analyzeAnnotation(SimpleXMLElement $annot, Doc\Document $doc)
    {
        $annot = $this->setupNs($annot);

        //Extract Term and build a Term object
        $_termVal = $annot->xpath('ao:body');
        $term     = (string) array_shift($_termVal);

        //Extract Topics
        foreach($annot->xpath('ao:hasTopic') as $topic) {

            //Attempt to get it from the hasTopic['rdf:resource'] attribute
            $topicUri = (string) $topic[0]->attributes('rdf', true)->resource;

            //If topicUri didn't work that way, then it is in the rdf:Description child node..
            if (empty($topicUri)) {

                $topicUris = array();

                //Get the topic from the rdf:Description child
                $desc = $topic[0]->children('rdf', true)->Description;
                $topicUris[] = (string) $desc[0]->attributes('rdf', true)->about; 

                //Also get the seeAlso's...
                foreach($desc[0]->children('rdfs', true)->seeAlso as $seeAlso) {
                    $topicUris[] = (string) $seeAlso[0]->attributes('rdf', true)->resource;
                }                    
            }
            else {
                $topicUris[] = array($topicUri);
            }
        }

        //Derive vocab and number of instances
        $vocab     = $this->deriveVocabForTopic($topicUri);
        $instances = $this->getNumberOfContextsForAnnotation($annot);

        //Return ttv
        return $ttv;
    }


    // --------------------------------------------------------------

    /**
     * Derive Vocabulary URI from topic URI
     *
     * @var    string          $topicUri
     * @return string|boolean  Vocabulary URI, or false if not on the list of vocabs
     */
    public function deriveVocabForTopic($topicUri)
    {
        foreach($this->vocabList as $shortName => $vocabUri) {

            if (strcasecmp($vocabUri, substr($topicUri, 0, strlen($vocabUri))) == 0) {
                return $vocabUri;
            }
        }

        //If Made it here, return false for no voabulary found
        return false;
    }   

    // --------------------------------------------------------------

    /**
     * Shortcut helper to extract information from XPATH more quickly
     */
    private function xpath(SimpleXMLElement $xml, $path)
    {
        $result = $xml->xpath($path);

        return (is_array($result) && count($result) == 1)
            ? (string) array_shift($result)
            : $result;
    }


    // --------------------------------------------------------------

    /**
     * Setup namespaces for XML
     *
     * @param  SimpleXMLElement $xml
     * @return SimpleXmlElement
     */
    private function setupNs(SimpleXMLElement $xml)
    {
        $xml->registerXPathNamespace('ao', 'http://purl.org/ao/core/');
        $xml->registerXPathNamespace('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
        $xml->registerXPathNamespace('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
        $xml->registerXPathNamespace('owl', 'http://www.w3.org/2002/07/owl#');
        $xml->registerXPathNamespace('bibo', 'http://purl.org/ontology/bibo/');
        $xml->registerXPathNamespace('doco', 'http://purl.org/spar/doco/');
        $xml->registerXPathNamespace('sioc', 'http://rdfs.org/sioc/ns#');
        $xml->registerXPathNamespace('foaf', 'http://xmlns.com/foaf/0.1/');
        $xml->registerXPathNamespace('dcterms', 'http://purl.org/dc/terms/');
        $xml->registerXPathNamespace('xsp', 'http://www.owl-ontologies.com/2005/08/07/xsp.owl');
        $xml->registerXPathNamespace('cnt', 'http://www.w3.org/2011/content#');
        $xml->registerXPathNamespace('prov', 'http://www.w3.org/ns/prov#');

        return $xml;
    } 
}

/* EOF: BioteaRdfSetParser.php */