<?php

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
class Topic
{
    /** @Id @GeneratedValue @Column(type="integer") **/
    protected $id;

    /** @Column(type="string") **/
    protected $uri;

    /** @Column(type="string", nullable=true) **/
    protected $shortName; 

    /**
     * @ManyToOne(targetEntity="Vocabulary", inversedBy="topics")
     **/
    private $vocabulary;

    /**
     * @ManyToMany(targetEntity="Term", mappedBy="topics")
     * @JoinTable(name="JoinTermsTopics")
     **/
    private $terms;

    // --------------------------------------------------------------

    public function __construct($uri, $shortName = null)
    {
        $this->terms = new ArrayCollection();

        $this->uri = $uri;
        $this->setShortName($shortName);
    }

    // --------------------------------------------------------------

    public function __get($val)
    {
        return $this->$val;
    }
    
    // --------------------------------------------------------------

    public function __toString()
    {
        return $this->getUri();
    }

    // --------------------------------------------------------------

    public function getId()
    {
        return $this->id;
    }
    
    // --------------------------------------------------------------

    public function getUri()
    {
        return $this->uri;
    }

    // --------------------------------------------------------------

    public function setShortName($name)
    {
        $this->shortName = $name;
    }

    // --------------------------------------------------------------

    public function getShortName()
    {
        return $this->shortName;
    }

    // --------------------------------------------------------------

    public function getVocabulary()
    {
        return $this->vocabulary;
    }

    // --------------------------------------------------------------

    public function setVocabulary(Vocabulary $vocabulary)
    {
        $this->vocabulary = $vocabulary;
    }

    // --------------------------------------------------------------

    public function getTerms()
    {
        return $this->terms;
    }
    
    // --------------------------------------------------------------
}

/* EOF: Topic.php */