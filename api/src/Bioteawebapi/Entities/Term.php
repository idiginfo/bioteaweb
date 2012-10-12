<?php

namespace Bioteawebapi\Entities;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Annotation Entity represents Indexes for a Biotea Annotation
 * 
 * @Entity
 */
class Term
{
     /** @Id @GeneratedValue @Column(type="integer") */
    protected $id;

    /** @Column(type="string") **/
    protected $term;

    /**
     * @ManyToMany(targetEntity="Topic", mappedBy="terms")
     **/
    private $topics;

    /**
     * @OneToMany(targetEntity="Annotation", mappedBy="term")
     **/    
    private $annotations;

    public function __construct($term, $topics)
    {
        $this->annotations = new ArrayCollection();
        $this->topics = new ArrayCollection();
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