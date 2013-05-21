<?php

namespace Bioteardf\Model\Doc;

use Doctrine\Common\Collections\ArrayCollection;
use Bioteardf\Helper\DocIndexEntity;

/**
 * Document
 * 
 * @Entity
 * @Table(uniqueConstraints={
 *   @UniqueConstraint(name="pmid", columns={"pmid"})
 * }) 
 */
class Document extends DocIndexEntity
{  
    /**
     * @var string
     * @Column(type="string")     
     */
    protected $pmid;

    /**
     * @var Doctrine\Common\Collections\ArrayCollection
     * @OneToMany(targetEntity="Paragraph", mappedBy="document")
     */    
    protected $paragraphs;    

    /**
     * @var Journal
     * @ManyToOne(targetEntity="Journal", inversedBy="documents")
     */
    protected $journal;

    // --------------------------------------------------------------

    public function __construct($pmid = null)
    {
        $this->pmid = $pmid;

        $this->sections = new ArrayCollection();
        
        $this->locallyUniqueId = (string) $this;        
    }

    // ----------------------------------------------------------------

    public function __tostring()
    {
        return $this->pmid;
    }

    // --------------------------------------------------------------

    /** 
     * @var string $pmid
     */
    public function setPmid($pmid)
    {
        $this->pmid = (string) $pmid;
    }

    // --------------------------------------------------------------

    public function setJournal(Journal $journal)
    {
        $this->journal = $journal;
    }

    // --------------------------------------------------------------

    public function addParagraph(Paragraph $paragraph)
    {
        $this->paragraphs[] = $paragraph;
    } 
}


/* EOF: Document.php */