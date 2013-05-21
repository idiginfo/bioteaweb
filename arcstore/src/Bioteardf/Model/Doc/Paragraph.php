<?php

namespace Bioteardf\Model\Doc;

use Doctrine\Common\Collections\ArrayCollection;
use Bioteardf\Helper\DocIndexEntity;

/**
 * Paragraph
 * 
 * @Entity
 */
class Paragraph extends DocIndexEntity
{
    /**
     * @var string
     * @Column(type="string") 
     */
    protected $content;

    /**
     * @var string
     * @Column(type="text") 
     */
    protected $identifier;

    /**
     * @var Section
     * @ManyToOne(targetEntity="Document", inversedBy="paragraphs")
     */
    protected $document;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="TermInstance", mappedBy="paragraph")
     */
    protected $termInstances;

    // --------------------------------------------------------------

    public function __construct($identifier, $content, Document $document)
    {
        $this->identifier    = $identifier;
        $this->content       = $content;
        $this->termInstances = new ArrayCollection();
        $this->document      = $document;

        $this->locallyUniqueId = hash('sha256', $identifier);      
    }

    // ----------------------------------------------------------------

    public function __tostring()
    {
        return $this->identifier;
    }

    // --------------------------------------------------------------

    public function addTermInstance(TermInstance $termInstance)
    {
        $this->termInstances[] = $termInstance;
    }   
}

/* EOF: Paragraph.php */