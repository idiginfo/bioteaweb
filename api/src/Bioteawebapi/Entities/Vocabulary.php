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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

/**
 * Vocabulary Entity
 * 
 * @Entity
 * @Table(uniqueConstraints={
 *   @UniqueConstraint(name="uri", columns={"uri"}),
 *   @UniqueConstraint(name="shortName", columns={"shortName"})
 * })
 */
class Vocabulary extends Entity
{
    /** @Id @GeneratedValue @Column(type="integer") **/
    protected $id;

    /** @Column(type="string") **/
    protected $uri;

    /** @Column(type="string", nullable=true) **/
    protected $shortName;

    /**
     * @OneToMany(targetEntity="Topic", mappedBy="vocabulary")
     **/
    private $topics;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param string $uri
     * @param string $shortName
     */
    public function __construct($uri, $shortName = null)
    {
        $this->topics = new ArrayCollection();

        //Set parameters
        $this->uri = $uri;
        $this->shortName = $shortName;
    }

    // --------------------------------------------------------------

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getUri();
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
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    // --------------------------------------------------------------

    /**
     * @return string
     */
    public function getShortName()
    {
        return (string) $this->shortName;
    }

    // --------------------------------------------------------------

    /**
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getTopics()
    {
        return $this->topics;
    }   

    // --------------------------------------------------------------

    public function getTerms()
    {
        $terms = array();

        foreach($this->getTopics() as $topic) {
            $terms = array_merge($terms, $topic->getTerms()->toArray());
        }

        return new ArrayCollection(array_values($terms));
    }

    // --------------------------------------------------------------

    public function getDocuments()
    {
        $docs = array();

        foreach($this->getTerms() as $term) {
            $docs = array_merge($docs, $term->getDocuments()->toArray());
        }
        
        return new ArrayCollection(array_values($docs));
    }
 
}

/* EOF: Vocabulary.php */