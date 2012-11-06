<?php

/**
 * Bioteaweb API
 *
 * A rest API frontend and indexer for the Biotea RDF project
 *
 * @link    http://biotea.idiginfo.org/api
 * @author  Casey McLaughlin <caseyamcl@gmail.com>
 * @license Copyright (c) Florida State University - All Rights Reserved
 */

// ------------------------------------------------------------------

namespace Bioteawebapi\Services\Indexer;
use Doctrine\ORM\EntityManager;
use Bioteawebapi\Entities\Document;
use ReflectionObject;

/**
 * Index Persister class persists non-database-aware entity graphs
 *
 * This exists, because I could not get the Doctrine ORM to persist
 * the graph without errors all over the place, and because Doctrine
 * doesn't support UPSERT or REPLACE INTO.  Perhaps in D2.3 (final) or 2.4,
 * this functionality will exist, and we can remove this class.
 *
 * Another possibility is to use Doctrine to persist the objects, but do
 * it in a way where the relationships aren't cascaded, but instead handled
 * manually.  This too causes problems when I try to do it (2012-oct 16 Doctrine 2.3dev),
 * because the ORM attempts to insert multiple joins for the same records on
 * many-to-many bidirectional relationships.
 *
 * In the meantime, this works pretty well, but is tightly coupled with Entities
 */
class IndexPersister
{
    /**
     * @const MySQL Mode .. If TRUE, then the faster, non-db agonstic MySQL code is used
     */
    const MYSQL_MODE = true;

    /**
     * @var Doctrine\DBAL\Connection
     */
    private $dbal;

    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var array  Cached table info
     */
    private $cachedTableInfo;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        //Setup entityManager and unitOfWork
        $this->em   = $em;
        $this->dbal = $this->em->getConnection();

        //Setup cached table info
        $ths->cachedTableInfo = array();
    }

    // --------------------------------------------------------------

    /**
     * Performs a quick and dirty query against the database for document path
     *
     * @param string $path  Relative document path to main RDF file, as stored in databse
     * @return boolean      True if exists, false if doesn't exist
     */
    public function checkDocumentExistsByPath($path)
    {
        $stmt = $this->dbal->prepare("SELECT * FROM Document WHERE rdfFilePath = ?");
        $stmt->bindvalue(1, $path);
        if ($stmt->execute()) {
            return ($stmt->rowCount() > 0);
        }
        else {
            return false;
        }
        
    }

    // --------------------------------------------------------------

    /**
     * Manually persist a document entity and all of its graphed objects into the database,
     * since Doctrine seems to have major issues with this
     *
     * @param Entities\Document $document
     * @return boolean  Whether the document was inserted or not (false if it already exists)
     */
    public function persistDocument(Document $document)
    {
        //Reset pending inserts
        $this->pendingInserts = array();

        //Set Entity Manager to clean state
        $this->em->clear();

        //If the document already exists, just return false
        if ($this->checkItemExists($document)) {
            return false;
        }

        //Index a single document in the database
        $this->dbal->beginTransaction();
        try {

            //Insert the document itself into the documents table
            $docCols = $this->getInsertDataForEntity($document);
            $this->conditionalInsertEntity($document, $docCols);

            //Do each annotations
            foreach($document->getAnnotations() as $annot) {

                //Get the term
                $term = $annot->getTerm();

                //Add the term or assign its ID
                $termCols = $this->getInsertDataForEntity($term);
                $this->conditionalInsertEntity($term, $termCols);

                //Insert the topic for the term
                foreach($term->getTopics() as $topic) {

                    $topicCols = $this->getInsertDataForEntity($topic);

                    //And the vocabulary, if it exists
                    if ($topic->getVocabulary()) {
                        $vocab = $topic->getVocabulary();
                        $this->conditionalInsertEntity($vocab, $this->getInsertDataForEntity($vocab));
                        $topicCols['vocabulary_id'] = $vocab->getId();
                    }

                    $this->conditionalInsertEntity($topic, $topicCols);

                    //Do the relationship between the term and the topic
                    $term_id = $term->getId();
                    $topic_id = $topic->getId();

                    if (self::MYSQL_MODE) {

                        $q = $this->dbal->prepare(
                            "INSERT INTO term_topic (term_id, topic_id)
                             VALUES (?, ?) ON DUPLICATE KEY UPDATE term_id=term_id;"
                        );
                        $q->bindValue(1, $term_id);
                        $q->bindValue(2, $topic_id);

                        $q->execute();
                    }
                    else {

                        $q = "SELECT * FROM term_topic WHERE term_id = ? AND topic_id = ?";
                        if ( ! $this->dbal->fetchArray($q, array($term_id, $topic_id))) {
                            $this->dbal->insert('term_topic', array('term_id' => $term_id, 'topic_id' => $topic_id));
                        }   

                    }                     
                }

                //Add the annotation
                $annotCols['document_id'] = $document->getId();
                $annotCols['term_id']     = $annot->getTerm()->getId();
                $this->conditionalInsertEntity($annot, $annotCols);
            }

            //Transaction commit!
            $this->dbal->commit();

            //Return true
            return true;

        } catch(\Exception $e) {
            $this->dbal->rollback();
            throw $e;
        }
    }

    // --------------------------------------------------------------

    /**
     * Insert and/or assign IDS to a set of entities
     *
     * If the entity already exists in the database (has unqiue indexes
     * that match an existing record), then the method will simply 
     * assign the ID to that object using reflection.  If not, an insert
     * query will run, and the ID will be assigned from the result of that record
     *
     * @param  object   $entities  Array of entities
     * @param  array    $cols      Columns to use for the INSERT statement
     * @return int                 The item ID
     */
    protected function conditionalInsertEntity($entity, Array $cols)
    {
        //MySQL Mode?
        if (self::MYSQL_MODE) {

            //build the SQL
            $sql = sprintf(
                "INSERT INTO %s (%s) VALUES (%s) ON DUPLICATE KEY UPDATE `id`=LAST_INSERT_ID(`id`);",
                $this->getTableNameForEntity($entity),
                "`" . implode("`, `", array_keys($cols)) . "`",
                str_repeat(("?, "), count($cols)-1) . "?"
            );


            //Build the statement
            $stmt = $this->dbal->prepare($sql);
            $colVals = array_values($cols);
            for ($n = 0; $n < count($colVals); $n++) {
                $stmt->bindValue($n+1, $colVals[$n]);
            }

            //Execute it and get the id
            $stmt->execute();
            $id = $this->dbal->lastInsertId();
        }
        else {  //DB Agnostic (slower) way

            //If item exists, then we simply apply the ID to the entity
            if ( ! $id = $this->checkItemExists($entity)) {

                $this->dbal->insert($this->getTableNameForEntity($entity), $cols);
                $id = $this->dbal->lastInsertId();
            }
        }

        $this->setId($entity, $id);
        return $id;
    }

    // --------------------------------------------------------------

    /**
     * Use reflection to manually set the id of an entity object
     *
     * @param $entity
     * @param int $id
     */
    protected function setId($entity, $id)
    {
        $refObject = new ReflectionObject($entity);
        $refProperty = $refObject->getProperty('id');
        $refProperty->setAccessible(true);
        $refProperty->setValue($entity, (int) $id);
    }

    // --------------------------------------------------------------

    /**
     * Check if an entity exists (if it has an id, or if its unique indexable columns
     * and those columns already exists)
     *
     * @return int  The item ID or null if it is new
     */
    protected function checkItemExists($entity)
    {
        //If an ID exists, just return that
        if ($entity->getId()) {
            return $entity->getId();
        }

        //Set itemId to null unless we find something
        $itemId = null;

        //Entity name
        $entityName = get_class($entity);
        $metadata = $this->em->getClassMetadata($entityName);

        //Get db metadata about the item through Doctrine method calls on it
        //what table?  What are the unique indexes that are set?
        $indexColumns = $this->getUniqueFieldsForEntity($entityName);

        //Build query parameters based off unique index columns
        $queryParams = array();
        foreach($indexColumns as $fieldName) {
            $fieldVal = $metadata->getFieldValue($entity, $fieldName);

            if ($fieldVal) {
                $queryParams[$fieldName] = $fieldVal;
            }
        }

        //Does the query if we have anything to base it off of
        if (count($queryParams) > 0) {
            $rec = $this->em->getRepository($entityName)->findOneBy($queryParams);
        }
        else {
            $rec = false;
        }

        //Return result
        return ($rec) ? $rec->getId() : $itemId;
    }

    // --------------------------------------------------------------

    /**
     * Get indexed fields for an entity
     *
     * @param string $entityName   Fully qualfieid namespaced classname
     * @return array   Keys are column names, values are field names
     */
    private function getUniqueFieldsForEntity($entityName)
    {
        //Have cached data?
        if (isset($this->cachedTableInfo[$entityName])) {
            return $this->cachedTableInfo[$entityName];
        }

        $indexColumns = array();
        $metadata = $this->em->getClassMetadata($entityName);

        //If unique constraints exist...
        if (isset($metadata->table['uniqueConstraints'])) {
            foreach ($metadata->table['uniqueConstraints'] as $idxName => $idxInfo) {
                foreach ($idxInfo['columns'] as $col) {
                    $indexColumns[$col] = $metadata->fieldNames[$col];
                }
            }
        }

        $this->cachedTableInfo[$entityName] = $indexColumns;
        return $indexColumns;
    }

    // --------------------------------------------------------------

    /**
     * Get the table name for an entity used for insert queries
     *
     * @param object $entity
     * @return string
     */
    private function getTableNameForEntity($entity)
    {
        $metadata = $this->em->getClassMetadata(get_class($entity));
        return $metadata->table['name'];
    }

    // --------------------------------------------------------------

    /**
     * Get insert data for an entity to send to DBAL\Connection->insert()
     *
     * @param object $entity
     * @return array
     */
    private function getInsertDataForEntity($entity)
    {
        //Determine persistable fields in the entity
        $metadata = $this->em->getClassMetadata(get_class($entity));     

        $outArr = array();
        foreach($metadata->fieldNames as $colName => $fieldName) {
            $outArr[$colName] = $metadata->getFieldValue($entity, $fieldName);
        }

        //Return array that can be sent to DBAL\Connection->insert()
        return $outArr;
    }
}

/* EOF: IndexPersister.php */