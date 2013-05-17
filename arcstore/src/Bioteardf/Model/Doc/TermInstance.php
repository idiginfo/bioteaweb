<?php

namespace Bioteardf\Model\Doc;

use Doctrine\Common\Collections\ArrayCollection;
use Bioteardf\Helper\BaseEntity;

/**
 * Term Instance
 * 
 * @Entity
 * @Table(uniqueConstraints={
 *   @UniqueConstraint(name="identifier", columns={"identifier"})
 * })  
 */
class TermInstance extends BaseEntity
{
    /**
     * @var int
     * @Column(type="integer") 
     */
    protected $startChar;

    /**
     * @var int
     * @Column(type="integer") 
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
        $this->annotation = $annotation;
        $this->startChar  = (int) $startChar;
        $this->endChar    = (int) $endChar;

        //Build identifier
        $this->identifier = (string) $paragraph . ':::' . (string) $annotation->term;
        if ($startChar && $endChar) {
            $this->identifier .= ':::' . $startChar . ':::' . $endChar;
        }
    }

    // ----------------------------------------------------------------

    public function __tostring()
    {
        return $this->identifier;
    }
}

/* EOF: TermInstance.php */