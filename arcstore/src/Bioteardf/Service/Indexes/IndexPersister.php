<?php

namespace Bioteardf\Service\Indexes;

use Doctrine\ORM\EntityManager;
use Bioteardf\Service\RdfSetTracker;
use Bioteardf\Model\BioteaRdfSet;
use Bioteardf\Model\Doc;

/**
 * Persist a graph of indexes
 */
class IndexPersister
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var Bioteardf\Service\RdfSetTracker
     */
    private $tracker;

    // --------------------------------------------------------------

    public function __construct(EntityManager $em, RdfSetTracker $tracker)
    {
        $this->em      = $em;
        $this->tracker = $tracker;
    }

    // --------------------------------------------------------------

    public function persist(Doc\Document $doc)
    {
        //First, if the document has already been processed, throw new
        //PersistException

        //Reduce all vocabularies to their unique

        //Record that this document has been persisted.
    }
}

/* EOF: IndexPersister.php */