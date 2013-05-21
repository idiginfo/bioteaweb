<?php

namespace Bioteardf\Model\Doc;

use Doctrine\Common\Collections\ArrayCollection;
use Bioteardf\Helper\DocIndexEntity;

/**
 * Term Instance
 * 
 * @Entity
 * @Table(uniqueConstraints={
 *   @UniqueConstraint(name="identifier", columns={"identifier"})
 * })  
 */
class TermInstance extends DocIndexEntity
{
    /**
     * @var int
     * @Column(type="integer",nullable=true)
     */
    protected $startChar;

    /**
     * @var int
     * @Column(type="integer",nullable=true) 
     */
    protected $endChar;

    /**
     * @var Paragraph
     * @ManyToOne(targetEntity="Paragraph", inversedBy="termInstances")
     */
    protected $paragraph;

    /**
     * @var Annotation
     * @ManyToOne(targetEntity="Annotation", inversedBy="termInstances")
     */
    protected $annotation;

    /**
     * @var string  Calculated value based on paragraph and start/end char
     * @Column(type="string")      
     */
    protected $identifier;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param int $startChar
     * @param int $endChar
     */
    public function __construct(Paragraph $paragraph, Annotation $annot, $startChar = null, $endChar = null)
    {
        //Need all of these
        $this->paragraph  = $paragraph;
        $this->annotation = $annot;
        $this->startChar  = ($startChar !== null) ? (int) $startChar : null;
        $this->endChar    = ($endChar !== null)   ? (int) $endChar : null;

        //Build identifier
        $this->identifier = (string) $paragraph . ':::' . (string) $annot->term;
        if ($startChar && $endChar) {
            $this->identifier .= ':::' . $startChar . ':::' . $endChar;
        }

        $this->locallyUniqueId = (string) $this;        
    }

    // ----------------------------------------------------------------

    public function __tostring()
    {
        return $this->identifier;
    }
}

/* EOF: TermInstance.php */