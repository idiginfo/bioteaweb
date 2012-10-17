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
 */
class Annotation extends Entity
{
    /** 
     * @var int
     * @Id @GeneratedValue @Column(type="integer") 
     **/
    protected $id;

    /**
     * @var Term
     * @ManyToOne(targetEntity="Term", inversedBy="annotations", cascade={"persist", "merge"})
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
    public function __toString()
    {
        return (string) $this->getTerm();
    }

    // --------------------------------------------------------------

    /**
     * @return string
     */
    public function getTerm()
    {
        return $this->term;
    }

    // --------------------------------------------------------------

    /**
     * @return Document
     */
    public function getDocument()
    {
        return $this->document;
    }
}

/* EOF: Document.php */