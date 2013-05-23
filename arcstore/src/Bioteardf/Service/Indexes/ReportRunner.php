<?php

namespace Bioteardf\Service\Indexes;

use Pimple;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Component\Console\Output\OutputInterface;
use PDO;

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

    public function outputReport($reportKey, OutputInterface $output)
    {
        $sql = $this->reportBag[$reportKey]->getSQL();
        $stmt = $this->dbal->prepare($sql);
        $stmt->execute();
        $this->printReport($stmt, $output);
    }    

    // --------------------------------------------------------------

    protected function printReport($statement, OutputInterface $output)
    {
        $headersPrinted = false;

        while($row = $statement->fetch(PDO::FETCH_ASSOC)) {

            if ( ! $headersPrinted) {
                $output->writeln($this->getCSV(array_keys($row)));
                $headersPrinted = true;
            }

            $output->writeln($this->getCSV($row));
        }
    }

    // --------------------------------------------------------------

    /**
     * Get CSV from array
     *
     * @param array $data
     * @return string $csv
     */
    protected function getCSV(array $data)
    {
        $outstream = fopen("php://memory", 'r+');
        fputcsv($outstream, array_values($data));
        rewind($outstream);
        $csv = trim(fgets($outstream));
        fclose($outstream);
        return $csv;
    }
}

/* EOF: ReportRunner.php */