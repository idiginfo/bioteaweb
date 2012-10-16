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
 * Annotation Entity represents Indexes for a Biotea Annotation
 * 
 * @Entity
 * @Table(uniqueConstraints={
 *   @UniqueConstraint(name="term", columns={"term"})
 * }) 
 */
class Term
{
     /** @Id @GeneratedValue @Column(type="integer") */
    protected $id;

    /** @Column(type="string") **/
    protected $term;

    /**
     * @ManyToMany(targetEntity="Topic", inversedBy="terms", cascade={"persist", "merge"})
     **/
    private $topics;

    /**
     * @OneToMany(targetEntity="Annotation", mappedBy="term")
     **/    
    private $annotations;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param string $term
     * @param array $topics  Array of topic objects
     */
    public function __construct($term, Array $topics = array())
    {
        $this->annotations = new ArrayCollection();
        $this->topics = new ArrayCollection();

        //Set the term
        $this->term = strtolower($term);

        //Set the topics
        foreach($topics as $topic) {
            $this->addTopic($topic);
        }
    }

    // --------------------------------------------------------------

    public function __toString()
    {
        return $this->getTerm();
    }

    // --------------------------------------------------------------

    public function getId()
    {
        return $this->id;
    }
    
    // --------------------------------------------------------------

    public function getTerm()
    {
        return $this->term;
    }

    // --------------------------------------------------------------

    public function getAnnotations()
    {
        return $this->annotations;
    }

    // --------------------------------------------------------------

    public function getTopics()
    {
        return $this->topics;
    }

    // --------------------------------------------------------------

    /**
     * Add a topic to this term
     *
     * @param Topic $topic
     */
    public function addTopic(Topic $topic)
    {
        $this->topics[] = $topic;
    }
}


/* EOF: Term.php */