<?php

namespace Bioteawebapi\Entities;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

/**
 * Annotation Entity represents Indexes for a Biotea Annotation
 * 
 * @Entity
 */
class Annotation
{
    /** 
     * @var int
     * @Id @GeneratedValue @Column(type="integer") 
     **/
    protected $id;

    /**
     * @var Term
     * @ManyToOne(targetEntity="Term", inversedBy="annotations")
     **/
    protected $term;

    /**
     * @var Document
     * @ManyToOne(targetEntity="Document", inversedBy="annotations")
     */
    protected $document;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Term $term
     */
    public function __construct(Term $term)
    {
        $this->term = $term;
    }

    // --------------------------------------------------------------

    /**
     * Persist this item to the database
     *
     * @param Doctrine\ORM\EntityManager $em
     */
    public function persist(EntityManager $em)
    {
        $this->term->persist($em);
        $em->persist($this);
    }

    // --------------------------------------------------------------

    public function getTerm()
    {
        return $this->term;
    }

    // --------------------------------------------------------------

    public function getDocument()
    {
        return $this->document;
    }
}

/* EOF: Document.php */