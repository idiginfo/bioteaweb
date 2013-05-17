<?php

namespace Bioteardf\Model\Doc;

use Doctrine\Common\Collections\ArrayCollection;
use Bioteardf\Helper\BaseEntity;

/**
 * Document
 * 
 * @Entity
 * @Table(uniqueConstraints={
 *   @UniqueConstraint(name="pmid", columns={"pmid"}),
 *   @UniqueConstraint(name="md5",  columns={"md5"})
 * }) 
 */
class Document extends BaseEntity
{  
    /**
     * @var string
     * @Column(type="string")     
     */
    protected $pmid;

    /**
     * @var string  MD5 of the BioteaRdfSet
     * @Column(type="string") 
     */ 
    protected $md5;

    /**
     * @var Doctrine\Common\Collections\ArrayCollection
     * @OneToMany(targetEntity="Paragraph", mappedBy="document")
     */    
    private $paragraphs;    

    /**
     * @var Journal
     * @ManyToOne(targetEntity="Journal", inversedBy="documents")
     */
    private $journal;

    // --------------------------------------------------------------

    public function __construct($md5, $pmid = null)
    {
        $this->md5  = $md5;
        $this->pmid = $pmid;

        $this->sections = new ArrayCollection();
    }

    // ----------------------------------------------------------------

    public function __tostring()
    {
        return $this->md5;
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