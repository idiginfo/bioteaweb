<?php

namespace Bioteardf\Model\Doc;

use Doctrine\Common\Collections\ArrayCollection;
use Bioteardf\Helper\BaseEntity;

/**
 * Paragraph
 * 
 * @Entity
 * @Table(uniqueConstraints={
 *   @UniqueConstraint(name="identifier", columns={"identifier"})
 * })  
 */
class Paragraph extends BaseEntity
{
    /**
     * @var string
     * @Column(type="string") 
     */
    protected $content;

    /**
     * @var string
     * @Column(type="string") 
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

    public function __construct($identifier, $content)
    {
        $this->identifier    = $identifier;
        $this->content       = $content;
        $this->termInstances = new ArrayCollection();
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