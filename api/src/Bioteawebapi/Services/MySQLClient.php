<?php

namespace Bioteawebapi\Services;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Schema;

class MySQLClient
{
    /**
     * @var Doctrine\DBAL\Connection
     */
    private $dbal;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Doctrine\DBAL\Connection
     */
    public function __construct(Connection $dbal)
    {
        $this->dbal = $dbal;
    }

    // --------------------------------------------------------------

    /**
     * Index a document in MySQL
     * 
     * @param Models\BioteaDocSet $doc
     * @return array  Array of primary keys inserted for all tables
     */
    public function indexDocument(BiotaDocSet $doc)
    {

    }

    // --------------------------------------------------------------

    /**
     * Index topics and their associated vocabularies
     * 
     * @param Models\BioteaTopic $topic
     * @return array  Array of primary keys inserted 
     *                for vocabulary and topics tables
     */
    protected function indexTopic(BioteaTopic $topic)
    {

    }

    // --------------------------------------------------------------

    /**
     * Index terms and their related topics
     *
     * @param Models\BioteaTerm
     * @return array  Multi-dimensional array of primary keys 
     *                inserted for topics, terms, vocab tables
     */
    protected function indexTerm(BioteaTerm $term)
    {

    }

    // --------------------------------------------------------------
   
    /**
     * Index a vocbaulary
     *
     * @param string $uri
     * @param string $shortName
     * @return int id
     */
    protected function indexVocabulary($uri, $shortName)
    {

    }    

    // --------------------------------------------------------------
   
    /**
     * Insert or update data in a table depending on whether it already exists
     *
     * @TODO: CHANGE THIS TO PRIVATE (NOT PROTECTED)
     *
     * @param string $table  The table name
     * @param string $pk     The PK column name for the table
     * @param array $data    Array of key/value pairs
     * @return mixed         The primary key for the table
     */
    public function upsert($table, $pk, $data)
    {
        //Select query for the records
        $q = $this->dbal->createQueryBuilder();
        $q->from($table, 't');
        $q->add('select', "t." . $pk);
        foreach ($data as $col => $val) {
            $q->andWhere(sprintf("t.%s = '%s'", $col, $val));
        }

        $selectResult = $q->execute();
        $mode = ($selectResult->rowCount() > 0) ? 'update' : 'insert';

        //LEFT OFF HERE LEFT OFF HERE LEFT OFF HERE
        //Switch statement based on mode.  Insert or update and return PK
    }

    // --------------------------------------------------------------
   
    /**
     * Check if schema is up-to-date
     *
     * @param boolean $returnQuries  If true, will return an array of
     *                               queries to run instead of true/false
     * @return array|boolean
     */
    public function checkSchema($returnQueries = false)
    {
        //Get the current schema
        $sm = $this->dbal->getSchemaManager();
        $fromSchema = $sm->createSchema();
        $toSchema   = $this->getSchema();

        $comparator = new Comparator();
        $schemaDiff = $comparator->compare($fromSchema, $toSchema);
        $queries    = $schemaDiff->toSql($this->dbal->getDatabasePlatform());

        if ($returnQueries) {
            return $queries;
        }
        else {
            return (count($queries) == 0);
        }
    }

    // --------------------------------------------------------------
   
    /**
     * This method defines the schema
     *
     * @return Doctrine\DBAL\Schema\Schema
     */
    public function getSchema()
    {
        $schema = new Schema();

        //Documents Table
        $docTable = $schema->createTable('documents');
        $docTable->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $docTable->addColumn('rdfPath', 'string');
        $docTable->addColumn('rdfAnnotationPaths', 'string');
        $docTable->setPrimarykey(array('id'));

        //Terms Table
        $termsTable = $schema->createTable('terms');
        $termsTable->addColumn('term', 'string', array('length' => 255));
        $termsTable->setPrimaryKey(array('term'));

        //Vocabularies Table
        $vocabTable = $schema->createTable('vocabularies');
        $vocabTable->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));        
        $vocabTable->addColumn('uri', 'string', array('length' => 255));
        $vocabTable->addColumn('shortName', 'string', array('length' => 10));
        $vocabTable->addUniqueIndex(array('uri'));
        $vocabTable->addUniqueIndex(array('shortName'));
        $vocabTable->setPrimarykey(array('id'));

        //Topics Table
        $topicsTable = $schema->createTable('topics');
        $topicsTable->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $topicsTable->addColumn('uri', 'string', array('length' => 255));
        $topicsTable->addColumn('shortName', 'string', array('length' => 10));
        $topicsTable->addColumn('vocabulary_id', 'integer', array('unsigned' => true));
        $topicsTable->addUniqueIndex(array('uri'));
        $topicsTable->addUniqueIndex(array('shortName'));
        $topicsTable->setPrimarykey(array('id'));
        
        //Annotations Table
        $annotationsTable = $schema->createTable('annotations');
        $annotationsTable->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $annotationsTable->addColumn('document_id', 'integer', array('unsigned' => true));
        $annotationsTable->addColumn('term', 'string', array('length' => 255));
        $annotationsTable->setPrimarykey(array('id'));

        //Topics_for_terms Table
        $topicsForTermsTable = $schema->createTable('topics_for_terms');
        $topicsForTermsTable->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $topicsForTermsTable->addColumn('topic_id', 'integer', array('unsigned' => true));       
        $topicsForTermsTable->addColumn('term', 'string', array('length' => 255));               
        $topicsForTermsTable->setPrimarykey(array('id'));

        //Return the schema
        return $schema;        
    } 
}

/* EOF: MySQLClient.php */