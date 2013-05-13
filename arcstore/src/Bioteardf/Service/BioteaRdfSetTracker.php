<?php

namespace Bioteardf\Service;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Connection;

use Bioteardf\Helper\PersistableService;
use Bioteardf\Model\BioteaRdfSet;
use DateTime, PDO;

/**
 * Mapper interface to the database to track RDF sets
 * for state management purposes
 */
class BioteaRdfSetTracker implements PersistableService
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * @var Doctrine\DBAL\Connection
     */
    private $conn;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Doctrine\DBAL\Connection
     */
    public function __construct(Connection $conn, $tableName)
    {
        $this->conn      = $conn;
        $this->tableName = $tableName;
    }

    // --------------------------------------------------------------

    /**
     * Check to see if a particular RDF set has been processed already  
     *
     * @param Biotaerdf\Model\BioteaRdfSet $rdfSet
     * @return boolean  TRUE if processed; FALSE if not yet
     */
    public function isAlreadyProcessed(BioteaRdfSet $rdfSet)
    {
        $tableName = $this->tableName;
        $q = $this->conn->executeQuery("SELECT * FROM {$tableName} WHERE md5 = ? ", array($rdfSet->md5));
        return ($q->rowCount() > 0);
    }
 
    // --------------------------------------------------------------

    /**
     * Add or update a record in the database to indicate that a particular RDF dataset has been processed
     *
     * @param Biotaerdf\Model\BioteaRdfSet $rdfSet
     */
    public function recordAsProcessed(BioteaRdfSet $rdfSet)
    {
        return $this->conn->insert(
            $this->tableName,
            array('md5' => $rdfSet->md5, 'timestamp' => new DateTime()),
            array(PDO::PARAM_STR, 'datetime')
        );
    }
    
    // --------------------------------------------------------------

    /**
     * @return boolean  TRUE if up-to-date
     */
    public function isSetUp()
    {
        return (count($this->getSchemaDiffs()) == 0);
    }

    // --------------------------------------------------------------    

    /**
     * Setup the database
     *
     * @return int  The number of queries run
     */
    public function setUp()
    {
        $queries = $this->getSchemaDiffs();
        
        //Setup the tables in a transaction
        $conn =& $this->conn;
        $conn->transactional(function($conn) use ($queries) {            
            foreach ($queries as $q) {
                $conn->executeQuery($q);
            }
        });

        return count($queries);
    }

    // --------------------------------------------------------------    

    /**
     * Drop all data and tables for Tracking RdfSets
     *
     * @return boolean  TRUE if cleared, FALSE otherwise
     */
    public function reset()
    {
        $sm = $this->conn->getSchemaManager();
        $currentSchema = $sm->createSchema();
        $desiredSchema = clone $currentSchema;
        $desiredSchema->dropTable($this->tableName);
        $queries = $currentSchema->getMigrateToSql($desiredSchema, $this->conn->getDatabasePlatform());

        //Setup the tables in a transaction
        $conn =& $this->conn;
        $conn->transactional(function($conn) use ($queries) {            
            foreach ($queries as $q) {
                $conn->executeQuery($q);
            }
        });

        return ($this->isSetup() === false);
    }

    // --------------------------------------------------------------    

    /**
     * Return an array of queries to get the current db schema to the desired one
     *
     * @return array  Empty array if no queries (Everything up-to-date)
     */
    private function getSchemaDiffs()
    {
        //get schema manager ...
        $sm = $this->conn->getSchemaManager();

        $currentSchema = $sm->createSchema();
        $desiredSchema = new Schema();

        $myTable = $desiredSchema->createTable($this->tableName);
        $myTable->addColumn("id", "integer", array("unsigned" => true, "autoincrement" => true));
        $myTable->addColumn("md5", "string", array("length" => 32));
        $myTable->addColumn("timestamp", "datetime");
        $myTable->setPrimaryKey(array("id"));
        $myTable->addUniqueIndex(array("md5"));

        $comparator = new Comparator();
        $schemaDiff = $comparator->compare($currentSchema, $desiredSchema);
        return $schemaDiff->toSaveSql($this->conn->getDatabasePlatform());
    }        
}
/* EOF: BioteaRdfSetTracker.php */