<?php

namespace Bioteardf\Service;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Connection;

use Bioteardf\Helper\PersistableService;
use Bioteardf\Model\BioteaRdfSet;
use DateTime, PDO;

class MiscDataStore implements PersistableService
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
    public function __construct(Connection $conn)
    {
        $this->conn      = $conn;
        $this->tableName = 'misc_data';
    }

    // --------------------------------------------------------------

    /**
     * Create or update a value in the database uniquely identified by subject and item
     *
     * @param string $subject
     * @param string $item
     * @param scalar $value
     * @return boolean  TRUE if change made
     */
    public function putData($subject, $item, $value)
    {
        //Prepare query
        $tableName = $this->tableName;
        $stmt = $this->conn->prepare("REPLACE INTO {$tableName} (`subject`, `item`, `value`) VALUES (?, ?, ?)");
        $stmt->bindValue(1, $subject);
        $stmt->bindValue(2, $item);
        $stmt->bindValue(3, $value);

        $affected = $stmt->execute();
        return (boolean) $affected;
    }   


    // --------------------------------------------------------------

    /**
     * Increment a numerical value in the database by subject and item
     *
     * @param string $subject
     * @param string $item
     * @param int    $by
     * @return boolean  TRUE if change made
     */
    public function incrValue($subject, $item, $by = 1)
    {
        $tableName = $this->tableName;
        $stmt = $this->conn->prepare("UPDATE {$tableName} SET {$item}={$item}+1 WHERE $subject = ? AND $item = ?");
        $stmt->bindValue(1, $subject);
        $stmt->bindValue(2, $item);

        $affected = $stmt->execute();

        //the result of running the command is an integer of the number of rows
        //affected.  If == 0, then do another query to insert it
        if ($affected === 0) {
            $stmt = $this->conn->insert($this->tableName, array('subject' => $subject, 'item' => $item, 'value' => 1));
            $affected = $stmt->execute();
        }

        return (boolean) $affected;
    }

    // --------------------------------------------------------------

    /**
     * Get data for a subject and/or item
     *
     * @param string $subject
     * @param string $item
     * @return array|scalar  Scalar value for single item, or array for subject
     */
    public function getData($subject, $item = null)
    {
        $tableName = $this->tableName;

        if ($item) {
            $stmt = $this->conn->prepare("SELECT * FROM {$tableName} WHERE subject = ?")
            $stmt->bindValue(1, $subject);
        }
        else {
            $stmt = $this->conn->prepare("SELECT * FROM {$tableName} WHERE subject = ? AND item = ?")
            $stmt->bindValue(1, $subject);
            $stmt->bindValue(2, $item);
        }

        $stmt->execute();

        $arr = array();
        while ($row = $stmt->fetchAssoc()) {
            $arr[$row['item']] = $row['value'];
        }

        return ($item === null) ? array_shift($arr) : $arr;
    }

    
    // --------------------------------------------------------------

    /**
     * Get a list of subjects
     *
     * @return array  Array of subjects
     */
    public function getSubjects()
    {
        $tableName = $this->tableName;
        $stmt = $this->conn->prepare("SELECT subject FROM {$tableName} GROUP BY subject;");
        $stmt->execute();

        $arr = array();
        while ($row = $stmt->fetchAssoc()) {
            $arr[] = $row['subject'];
        }
        return $arr;
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

        $myTable->addColumn("id",        "integer", array("unsigned" => true, "autoincrement" => true));
        $myTable->addColumn("subject",   "string", array("length" => 50));
        $myTable->addColumn("item",      "string", array("length" => 50));
        $myTable->addColumn("value",     "text");
        $myTable->addColumn("timestamp", "datetime");

        $myTable->setPrimaryKey(array("id"));
        $myTable->addUniqueIndex(array("subject", "item"), 'subject_item');

        $comparator = new Comparator();
        $schemaDiff = $comparator->compare($currentSchema, $desiredSchema);
        return $schemaDiff->toSaveSql($this->conn->getDatabasePlatform());
    }     
}

/* EOF: MiscDataStore.php */