<?php

namespace Bioteardf\Service\Indexes;

use Doctrine\ORM\EntityManager;
use Bioteardf\Service\BioteaRdfSetTracker;
/**
 * Doc Object Persister
 */
class DocObjectPersister
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

    /**
     * Persist a Document and all of its items
     */
    public function persist(DocObjectRegistry $dor)
    {
        $graph = $dor->getGraph();

        //Things must be done in a particular order        
        $this->persistSet($graph, 'Document');
        $this->persistSet($graph, 'Journal');        
        $this->persistSet($graph, 'Paragraph');
        $this->persistSet($graph, 'Vocabulary');
        $this->persistSet($graph, 'Topic');
        $this->persistSet($graph, 'Term');
        $this->persistSet($graph, 'Annotation');
        $this->persistSet($graph, 'TermInstance');
        $this->em->flush();

        //Return true
        return true;
    }

    // --------------------------------------------------------------

    /**
     * Cleanup the memory space (use with caution!)
     */
    public function cleanup()
    {
        $this->em->clear();
    }

    // --------------------------------------------------------------

    /**
     * Persist set and remove it from the graph
     *
     * @param array  The graph
     * @param string $setName
     */
    private function persistSet(array $graph, $setName)
    {
        if (isset($graph[$setName])) {
            foreach($graph[$setName] as $obj) {
                $this->em->persist($obj);
            }
        }
    }


}

/* EOF: DocObjectPersister.php */