<?php

namespace Bioteardf\Model\Doc;

use Doctrine\Common\Collections\ArrayCollection;
use Bioteardf\Helper\BaseEntity;

/**
 * Journal Class
 * 
 * @Entity
 * @Table(uniqueConstraints={
 *   @UniqueConstraint(name="name", columns={"name"})
 * }) 
 */
class Journal extends BaseEntity
{   
    /**
     * @var string
     * @Column(type="string") 
     */
    protected $name;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="Document", mappedBy="journal")
     */
    protected $documents;

    // ----------------------------------------------------------------

    public function __tostring()
    {
        return $this->name;
    }

    // --------------------------------------------------------------

    public function __construct($name)
    {
        $this->name      = $name;
        $this->documents = new ArrayCollection();
    }

    // --------------------------------------------------------------

    public function addDocument(Document $document)
    {
        $this->documents[] = $documents;
    }

}

/* EOF: Journal.php */