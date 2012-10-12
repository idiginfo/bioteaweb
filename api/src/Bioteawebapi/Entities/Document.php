<?php

namespace Bioteawebapi\Entities;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Document Entity represents Indexes for a BioteaDocument
 * 
 * @Entity
 */
class Document
{
    /** @Id @GeneratedValue @Column(type="integer") **/
    protected $id;

    /** @Column(type="string") **/
    protected $rdfPath;

    /** @Column(type="text", nullable=true) **/
    protected $rdfAnnotationPaths;

    /**
     * @OneToMany(targetEntity="Annotation", mappedBy="document")
     **/    
    private $annotations;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @var string $rdfFilePath  Relative path to RDF file
     */
    public function __construct($rdfFilePath)
    {
        $this->setRdfFilePath($rdfFilePath);
        $this->annotations = new ArrayCollection();
    }

    // --------------------------------------------------------------

    /**
     * Add Annotation File
     * 
     * @param \SimpleXMLELement $xml             XML from the annotation file
     * @param string            $annotationName  Name for the Annotation file
     * @param string            $filepath        Relative to document basepath
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

        //Foreach annotation, do something..
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
            $this->terms[$term] = new Term($term);

            //Build the topics arrays
            foreach($topics as $topic) {
                $this->topics[$topic] = $this->buildTopicObj($topic);
                $this->terms[$term]->addTopic($this->topics[$topic]);
            }
        }
    }

    // --------------------------------------------------------------

    /**
     * Set RDF File Path
     *
     * @var string $rdfFilePath  Relative path to RDF file
     */
    public function setRdfFilePath($path)
    {
        $this->rdfPath = $path;
    }

    // --------------------------------------------------------------

    /** @return array Array of Vocabularies */
    public function getVocabularies()
    {

    }

    // --------------------------------------------------------------

    /** @return array Array of Topics */
    public function getTopics()
    {

    }

    // --------------------------------------------------------------

    /** @return array  Array of Terms */
    public function getTerms()
    {

    }

    // --------------------------------------------------------------
  
    public function getRDFPath()
    {

    }

    // --------------------------------------------------------------

    /** @return array */
    public function getRDFAnnotationPaths()
    {
        
    }
}

/* EOF: Document.php */