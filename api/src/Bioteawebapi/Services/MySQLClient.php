<?php

namespace Bioteawebapi\Services;
use Bioteawebapi\Exceptions\MySQLClientException;
use Bioteawebapi\Models\BioteaDocSet;
use Bioteawebapi\Models\BioteaTerm;
use Bioteawebapi\Models\BioteaTopic;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Schema;

class MySQLClient
{
    /**
     * @var Doctrine\DBAL\Connection
     */
    private $dbal;

    /**
     * @var array|null
     */
    private $trans;

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
    public function indexDocument(BioteaDocSet $doc)
    {
        //Start a transaction
        $this->trans = array();
        $this->dbal->beginTransaction();

        try {
            $this->indexBioteaDoc($doc);
            $this->dbal->commit();

            //End transaction
            $transStatus = $this->trans;
            $this->trans = null;
        }
        catch (\Exception $e) {
            $this->dbal->rollback();
            throw $e;
        }

        //Return all ids inserted
        return $this->countRowsInserted($transStatus);
    }

    // --------------------------------------------------------------

    /**
     * Return the number of rows inserted during a transaction
     *
     * @param array $rans
     * @return int
     */
    protected function countRowsInserted($trans)
    {
        $ct = 0;

        foreach ($trans as $tblName => $inserted) {
            $ct += count($inserted);
        }

        return $ct;
    }

    // --------------------------------------------------------------

    /**
     * Index a single biotea docset
     *
     * If determined that it is already indexed, then skip indexing
     * the terms, topics, and vocabularies
     *
     * @param Models\BioteaDocSet $doc
     * @return int  The Document ID in the database
     */
    protected function indexBioteaDoc(BioteaDocSet $doc)
    {
        //First add the document
        $data = array();
        $data['rdfPath'] = $doc->getMainFilePath();
        $data['rdfAnnotationPaths'] = json_encode($doc->getAnnotationFilePaths());
        $docId = $this->conditionalInsert('documents', $data);

        //If the document was just inserted, index all of its information
        if (isset($this->trans['documents']) && in_array($docId, $this->trans['documents'])) {

            //Add the Annotations
            foreach($doc->getTerms() as $term) {
                $this->indexAnnotation($term, $docId);
            }
        }

        return $docId;
    }

    // --------------------------------------------------------------

    /**
     * Index an annotation based on a term object
     *
     * In the future, we may create a separate model for the annotation
     * with additional information that wraps around the term object, but
     * for now, we can pass in the values for the items we wish to join
     *
     * @param Models\Bioteaterm $term
     * @param int $document_id
     * @return int  Annotation ID
     */
    protected function indexAnnotation(BioteaTerm $term, $docId)
    {
        //Index the term (and topics, vocabs, relationships)
        $termId = $this->indexTerm($term);

        $data = array();
        $data['term_id']     = $termId;
        $data['document_id'] = $docId;

        return $this->conditionalInsert('annotations', $data);
    } 

    // --------------------------------------------------------------

    /**
     * Index terms and their related topics
     *
     * @param Models\BioteaTerm $term
     * @return int  Term ID
     */
    protected function indexTerm(BioteaTerm $term)
    {
        //Index the term
        $data = array('term' => $term);
        $termId = $this->conditionalInsert('terms', $data);

        //Index the topics
        foreach($term->getTopics() as $topic) {
            $topicId = $this->indexTopic($topic);

            //Add a relationship
            $this->conditionalInsert(
                'topics_for_terms', 
                array('topic_id' => $topicId, 'term_id' => $termId)
            );
        }

        return $termId;
    }

    // --------------------------------------------------------------

    /**
     * Index topics and their associated vocabularies
     * 
     * @param Models\BioteaTopic $topic
     * @return int  Topic ID
     */
    protected function indexTopic(BioteaTopic $topic)
    {
        //Index the vocabulary if it is known
        if ($topic->getVocabularyUri() && $topic->getVocabularyShortName()) {
            $vocabId = $this->indexVocabulary($topic->getVocabularyUri(), $topic->getVocabularyShortName());
        }

        //Index the topic
        $data = array();
        $data['uri'] = $topic->getTopicUri();
        if ($topic->getTopicShortName()) {
            $data['shortName'] = $topic->getTopicShortName();
        }
        if (isset($vocabId)) {
            $data['vocabulary_id'] = $vocabId;
        }

        return $this->conditionalInsert('topics', $data);
    }

    // --------------------------------------------------------------
   
    /**
     * Index a vocbaulary
     *
     * @param string $uri
     * @param string $shortName
     * @return int Vocabulary ID
     */
    protected function indexVocabulary($uri, $shortName)
    {
        $data = array();
        $data['uri'] = $uri;
        $data['shortName'] = $shortName;

        return $this->conditionalInsert('vocabularies', $data);
    }    

    // --------------------------------------------------------------
   
    /**
     * Insert a record if it doesn't exist and return its ID
     *
     * @param string $table  The table name
     * @param array $data    Array of key/value pairs
     * @return int           The primary key ID for the table
     */
    private function conditionalInsert($table, $data)
    {
        //Determine which columns in the table are unique indicies, including ID
        $indexCols = array();
        $sm = $this->dbal->getSchemaManager();
        foreach($sm->listTableIndexes($table) as $index) {
            if ($index->isUnique()) {
                $indexCols = array_merge($indexCols, $index->getColumns());
            }
        }

        //If any of those columns are included in the $data, check to see if a
        //row already exists
        if (count(array_intersect($indexCols, array_keys($data))) > 0) {

            //Do a select based on those columns in the data
            $q = $this->dbal->createQueryBuilder();
            $q->select('t.id')->from($table, 't');
            foreach($indexCols as $icol) {
                if (isset($data[$icol])) {
                    $q->andWhere(sprintf("t.%s = '%s'", $icol, $this->quote($data[$icol])));
                }
            }
            $result = $q->execute();

            //If we get a result, simply return the ID.
            if ($result->rowCount() > 0) {
                return $result->fetchColumn(0);
            }            
        }

        //Otherwise, assume that we are going to insert..

        //Do the insert
        $this->dbal->insert($table, $data);
        $newId = $this->dbal->lastInsertId();

        //Record it in the trans
        if (is_array($this->trans)) {
            $this->trans[$table][] = $newId;
        }

        return $newId;
    }

    // --------------------------------------------------------------
   
    /**
     * Quote helper
     *
     * Doing a str_replace seems to be the only working
     * method.  Only used with the dbal QueryBuilder
     *
     * @param string $val
     * @return string
     */
    private function quote($val)
    {
        return (strpos($val, "'") !== false)
            ? str_replace("'", "\\'", $val)
            : $val;
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
        $docTable->addUniqueIndex(array('rdfPath'));
        $docTable->setPrimarykey(array('id'));

        //Terms Table
        $termsTable = $schema->createTable('terms');
        $termsTable->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $termsTable->addColumn('term', 'string', array('length' => 255));
        $termsTable->addUniqueIndex(array('term'));
        $termsTable->setPrimaryKey(array('id'));

        //Vocabularies Table
        $vocabTable = $schema->createTable('vocabularies');
        $vocabTable->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));        
        $vocabTable->addColumn('uri', 'string', array('length' => 255));
        $vocabTable->addColumn('shortName', 'string', array('length' => 25));
        $vocabTable->addUniqueIndex(array('uri'));
        $vocabTable->addUniqueIndex(array('shortName'));
        $vocabTable->setPrimarykey(array('id'));

        //Topics Table
        $topicsTable = $schema->createTable('topics');
        $topicsTable->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $topicsTable->addColumn('uri', 'string', array('length' => 255));
        $topicsTable->addColumn('shortName', 'string', array('length' => 10));
        $topicsTable->addColumn('vocabulary_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $topicsTable->addUniqueIndex(array('uri'));
        $topicsTable->setPrimarykey(array('id'));
        
        //Annotations Table
        $annotationsTable = $schema->createTable('annotations');
        $annotationsTable->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $annotationsTable->addColumn('document_id', 'integer', array('unsigned' => true));
        $annotationsTable->addColumn('term_id', 'integer', array('unsigned' => true));
        $annotationsTable->setPrimarykey(array('id'));
        $annotationsTable->addUniqueIndex(array('document_id', 'term_id'));

        //Topics_for_terms Table
        $topicsForTermsTable = $schema->createTable('topics_for_terms');
        $topicsForTermsTable->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $topicsForTermsTable->addColumn('topic_id', 'integer', array('unsigned' => true));       
        $topicsForTermsTable->addColumn('term_id', 'integer', array('unsigned' => true));               
        $topicsForTermsTable->setPrimarykey(array('id'));
        $topicsForTermsTable->addUniqueIndex(array('topic_id', 'term_id'));

        //Return the schema
        return $schema;        
    } 
}

/* EOF: MySQLClient.php */