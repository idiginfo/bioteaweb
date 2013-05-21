<?php

namespace Bioteardf\Service\Indexes;

use Doctrine\ORM\EntityManager;

class DocObjectRegistryFactory
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    // --------------------------------------------------------------

    /**
     * @param Doctrine\ORM\EntityManager
     */
    public function __construct(EntityManager $em = null)
    {
        $this->em = $em;
    }

    // --------------------------------------------------------------

    public function factory($pmid = null)
    {
        return new DocObjectRegistry($this->em, $pmid);
    }
}

/* EOF: DocObjectRegistryFactory.php */