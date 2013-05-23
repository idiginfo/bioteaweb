<?php

namespace Bioteardf\Model\Doc;

use Doctrine\Common\Collections\ArrayCollection;
use Bioteardf\Helper\DocIndexEntity;

/**
 * Journal Class
 * 
 * @Entity
 * @Table(uniqueConstraints={
 *   @UniqueConstraint(name="name", columns={"name"})
 * }) 
 */
class Journal extends DocIndexEntity
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

        $this->locallyUniqueId = strtolower((string) $this);
    }

    // --------------------------------------------------------------

    public function addDocument(Document $document)
    {
        $this->documents[] = $documents;
    }
}

/* EOF: Journal.php */