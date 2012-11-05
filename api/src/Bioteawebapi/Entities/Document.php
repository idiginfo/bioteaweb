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

    /** @Column(type="text", nullable=true) **/
    protected $md5;

    /**
     * @OneToMany(targetEntity="Annotation", mappedBy="document", cascade={"persist", "merge"})
     **/    
    private $annotations;


    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param string $rdfFilePath         Relative path to RDF file
     * @param string $md5                 MD5 of this document
     * @param array $annotationFilePaths  Relative path to annotation files (as array)
     */
    public function __construct($rdfFilePath, $md5 = null, $annotationFilePaths = array())
    {
        $this->annotations = new ArrayCollection();

        $this->setRdfFilePath($rdfFilePath);
        foreach($annotationFilePaths as $name => $path) {
            $this->addAnnotationFilepath($name, $path);
        }

        $this->setMd5($md5);
    }

    // --------------------------------------------------------------

    /**
     * Set RDF File Path
     *
     * @param string $rdfFilePath  Relative path to RDF file
     */
    public function setRdfFilePath($path)
    {
        $this->rdfFilePath = $path;
    }

    // --------------------------------------------------------------

    /**
     * Set MD5 for files
     *
     * @param string $md5
     */
    public function setMd5($md5)
    {
        $this->md5 = $md5;
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

        return array_unique(array_filter($vocabs));
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

        return array_unique($topics);
    }

    // --------------------------------------------------------------

    /** 
     * @return array  Array of Terms 
     */
    public function getTerms()
    {
        $terms = array_map(function($entity) {
            return $entity->getTerm();
        }, $this->getAnnotations()->toArray());

        return array_unique($terms);
    }

    // --------------------------------------------------------------
  
    /** 
     * @return string 
     */
    public function getRDFFilePath()
    {
        return $this->rdfFilePath;
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