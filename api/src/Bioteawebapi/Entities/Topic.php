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
 * Topic Entity
 * 
 * @Entity
 * @Table(
 *   uniqueConstraints={
 *     @UniqueConstraint(name="uri", columns={"uri"})
 *   },
 *   indexes={
 *     @index(name="shortName", columns={"shortName"})
 *   }
 * )
 */
class Topic extends Entity
{
    /** @Id @GeneratedValue @Column(type="integer") **/
    protected $id;

    /** @Column(type="string") **/
    protected $uri;

    /** @Column(type="string", nullable=true) **/
    protected $shortName; 

    /**
     * @ManyToOne(targetEntity="Vocabulary", inversedBy="topics", cascade={"persist", "merge"})
     **/
    private $vocabulary;

    /**
     * @ManyToMany(targetEntity="Term", mappedBy="topics")
     * @JoinTable(name="JoinTermsTopics")
     **/
    private $terms;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param string $uri
     * @param string $shortName  Optional
     */
    public function __construct($uri, $shortName = null)
    {
        $this->terms = new ArrayCollection();

        $this->uri = $uri;
        $this->setShortName($shortName);
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
     * @param string
     */
    public function setShortName($name)
    {
        $this->shortName = $name;
    }

    // --------------------------------------------------------------

    /**
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    // --------------------------------------------------------------

    /**
     * @return Vocbabulary|null
     */
    public function getVocabulary()
    {
        return $this->vocabulary;
    }

    // --------------------------------------------------------------

    /**
     * @param Vocabulary
     */
    public function setVocabulary(Vocabulary $vocabulary)
    {
        $this->vocabulary = $vocabulary;
    }

    // --------------------------------------------------------------

    /**
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getTerms()
    {
        return $this->terms;
    }
    
    // --------------------------------------------------------------
}

/* EOF: Topic.php */