<?php

namespace Bioteardf\Service\Indexes;

use Pimple;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * Report Runner
 */
class ReportRunner
{
    /**
     * @var Doctrine\DBAL\Connection
     */
    private $dbal;

    /**
     * @var Pimple
     */
    private $reportBag;

    // --------------------------------------------------------------

    /**
     * @param Doctrine\ORM\EntityManager
     */
    public function __construct(Pimple $reportBag, EntityManager $em = null)
    {
        $this->reportBag = $reportBag;
        $this->dbal = $em->getConnection();
    }

    // --------------------------------------------------------------

    public function getReportDescriptions()
    {
        $arr = array();
        foreach($this->reportBag->keys() as $key) {
            $arr[$key] = $this->reportBag[$key]->getDescription();
        }
        return $arr;
    }

    // --------------------------------------------------------------

    public function runReport($reportKey)
    {
        $dql = $this->reportBag[$reportKey]->getDQL();

        //Run it..
    }    
}

/* EOF: ReportRunner.php */