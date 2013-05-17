<?php

namespace Bioteardf\Service;

use Doctrine\ORM\EntityManager;
use Bioteardf\Model\BioteaRdfSet;

/**
 * RDF Set Tracker
 */
class RdfSetTracker
{
    /**
     * @param Doctrine\ORM\EntityManager
     */
    private $em;

    // --------------------------------------------------------------

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    // --------------------------------------------------------------

    public function actionAlreadyPerformed(BioteaRdfSet $rdfSet, $action)
    {
        $data = array('md5' => $rdfSet->md5, 'action' => $action);
        $result = $em->getRepository('Bioteardf\Model\RdfSetTracking')->findOneBy($data);

        return (boolean) $result;
    }

    // --------------------------------------------------------------

    public function recordAction(BioteaRdfSet $rdfSet, $action)
    {
        $obj = new RdfSetTracking($rdfSet->md5, $action);
        $this->em->persist($obj);
        $this->em->flush();
    }
}

/* EOF: RdfSetTracker.php */