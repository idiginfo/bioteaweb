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

namespace Bioteawebapi\Entities;
use Bioteawebapi\Services\RDFFileClient;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

/**
 * Document Entity represents Indexes for a BioteaDocument
 * 
 * @Entity
 * @Table(uniqueConstraints={
 *   @UniqueConstraint(name="rdfFilePath", columns={"rdfFilePath"})
 * })
 */
class Document extends Entity
{
    /** @Id @GeneratedValue @Column(type="integer") **/
    protected $id;

    /** @Column(type="string") **/
    protected $rdfFilePath;

    /** @Column(type="text", nullable=true) **/
    protected $rdfAnnotationPaths;

    /**
     * @OneToMany(targetEntity="Annotation", mappedBy="document", cascade={"persist", "merge"})
     **/    
    private $annotations;


    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @var string $rdfFilePath  Relative path to RDF file
     */
    public function __construct($rdfFilePath, $annotationFilePaths = array())
    {
        $this->annotations = new ArrayCollection();

        $this->setRdfFilePath($rdfFilePath);
        foreach($annotationFilePaths as $name => $path) {
            $this->addAnnotationFilepath($name, $path);
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
        $this->rdfFilePath = $path;
    }

    // --------------------------------------------------------------

    /**
     * Add Annotation File Path
     * 
     * @param string $annotationName
     * @param string $filepath
     */
    public function addAnnotationFilepath($annotationName, $filepath)
    { 
        $apaths = $this->getRDFAnnotationPaths();
        $apaths[$annotationName] = $filepath;

        $this->rdfAnnotationPaths = json_encode($apaths);
    }

    // --------------------------------------------------------------

    /**
     * Add Annotation
     *
     * @param Annotation
     */
    public function addAnnotation(Annotation $annotation)
    {
        $this->annotations[] = $annotation;
    }

    // --------------------------------------------------------------

    /**
     * Add Annotations
     *
     * @param array  Array of Annotation objects
     */
    public function addAnnotations(Array $annotations)
    {
        foreach($annotations as $annot) {
            $this->addAnnotation($annot);
        }
    }

    // --------------------------------------------------------------

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getRDFFilePath();
    }

    // --------------------------------------------------------------

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    // --------------------------------------------------------------

    /**
     * Get annotations
     *
     * @return array  Array of Annotation objects
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }

    // --------------------------------------------------------------

    /** 
     * @return array Array of Vocabularies 
     */
    public function getVocabularies()
    {
        $vocabs = array_map(function($entity) {
            return $entity->getVocabulary();
        }, $this->getTopics());

        return array_filter($vocabs);
    }

    // --------------------------------------------------------------

    /** 
     * @return array Array of Topics 
     */
    public function getTopics()
    {
        $topics = array();

        foreach($this->getTerms() as $termObj) {
            $topics = array_merge($topics, $termObj->getTopics()->toArray());
        }

        return $topics;
    }

    // --------------------------------------------------------------

    /** 
     * @return array  Array of Terms 
     */
    public function getTerms()
    {
        return array_map(function($entity) {
            return $entity->getTerm();
        }, $this->getAnnotations()->toArray());
    }

    // --------------------------------------------------------------
  
    /** 
     * @return string 
     */
    public function getRDFFilePath()
    {
        return $this->rdfPath;
    }

    // --------------------------------------------------------------

    /**
     * Returns an array of RDF Annotation URLs
     *
     * @param Biotaewebapi\Services\RDFFIleClient $resolver
     * @return string
     */
    public function getRDFFileUrl(RDFFileClient $resolver)
    {
        return $resolver->resolveUrl($this->getRdfFilePath());
    }

    // --------------------------------------------------------------

    /**
     * Returns an array of RDF Annotation URLs
     *
     * @param Biotaewebapi\Services\RDFFIleClient $resolver
     * @return array
     */
    public function getRDFAnnotationUrls(RDFFileClient $resolver)
    {
        $annots = $this->getRDFAnnotationPaths();
        foreach($annots as $name => &$path) {
            $path = $resolver->resolveUrl($path);
        }

        return $annots;
    }

    // --------------------------------------------------------------

    /**
     * @return array 
     */
    public function getRDFAnnotationPaths()
    {
       return json_decode($this->rdfAnnotationPaths, true) ?: array();
    } 
}

/* EOF: Document.php */