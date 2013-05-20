<?php

namespace Bitoeardf\Model;

use Bioteardf\Helper\BaseEntity as Entity;

/**
 * Represents an index of a Biotea document
 *
 * @Entity
 */
class DocIndex extends Entity
{
    /**
     * @var ArrayCollection
     * @OneToOne(targetEntity="BioteaRdfSet")
     */
    protected $rdfSet;

    /**
     * @var Bioteardf\Model\Doc\Document
     * @OneToOne(targetEntity="Doc\Document")
     */
    protected $document;

    /**
     * @var array
     */
    protected $terms = array();

    /**
     * @var array
     */
    protected $topics = array();

    /**
     * @var array
     */
    protected $vocabs = array();

    /**
     * @var array
     */
    protected $termInstances = array();

    /**
     * @var array
     */
    protected $paragraphs = array();

    /**
     * @var array
     */
    protected $annotations = array();  

    // --------------------------------------------------------------

    public function __construct(BioteaRdfSet $set)
    {
        $this->rdfSet = $set;
    }

    // --------------------------------------------------------------
    
    public function dispense($objectName)

}

/* EOF: DocIndex.php */