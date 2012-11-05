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
class Term extends Entity
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

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTerm();
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
     * @return Term
     */
    public function getTerm()
    {
        return $this->term;
    }

    // --------------------------------------------------------------

    /**
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }

    // --------------------------------------------------------------

    /**
     * Get documents that have this term
     *
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getDocuments()
    {
        $docs = array();
        foreach($this->getAnnotations() as $annot) {
            $doc = $annot->getDocument();
            $docs[$doc->getId()] = $doc;
        }

        return new ArrayCollection(array_values($docs));
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

    /**
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getVocabularies()
    {
        $vocabs = array();

        foreach($this->topics as $topic) {

            if ($vocab = $topic->getVocabulary()) {
                $vocabs[$vocab->getShortName()] = $vocab;
            }
        }

        return new ArrayCollection(array_values($vocabs));
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