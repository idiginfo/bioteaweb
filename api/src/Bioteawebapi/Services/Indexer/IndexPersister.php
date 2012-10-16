<?php

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
 * many-to-many bidirectional relationships
 */
class IndexPersister
{
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

        $this->dbal->beginTransaction();
        try {

            //Insert or assign ids for all vocabularies associated with this document
            foreach($document->getVocabularies() as $vocab) {
                $this->conditionalInsertEntity($vocab, $this->getInsertDataForEntity($vocab));
            }

            //Insert or assign ids for all topics associated with this document
            foreach($document->getTopics() as $topic) {

                $cols = $this->getInsertDataForEntity($topic);

                //Add the foreign key if there is one
                if ($topic->getVocabulary()) {
                    $cols['vocabulary_id'] = $topic->getVocabulary()->getId();
                }

                $this->conditionalInsertEntity($topic, $cols);
            }

            //Insert or assign ids for all terms associated with this document
            foreach($document->getTerms() as $term) {

                //Add the term or assign its ID
                $cols = $this->getInsertDataForEntity($term);
                $this->conditionalInsertEntity($term, $cols);

                //Add associations with topics
                foreach($term->getTopics() as $topic) {

                    $term_id = $term->getId();
                    $topic_id = $topic->getId();

                    $q = "SELECT * FROM term_topic WHERE term_id = ? AND topic_id = ?";
                    if ( ! $this->dbal->fetchArray($q, array($term_id, $topic_id))) {
                        $this->dbal->insert('term_topic', array('term_id' => $term_id, 'topic_id' => $topic_id));
                    }
                }
            }

            //Insert the document itself into the documents table
            $docCols = $this->getInsertDataForEntity($document);
            $this->conditionalInsertEntity($document, $docCols);

            //Insert all annotations associated with this document
            foreach($document->getAnnotations() as $annot) {
                $cols = $this->getInsertDataForEntity($annot);

                //Get the term ID
                $cols['term_id']     = $annot->getTerm()->getId();
                $cols['document_id'] = $document->getId();

                $this->conditionalInsertEntity($annot, $cols);
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
     * @return boolean  Whether or not the entity was inserted or merely updated
     */
    protected function conditionalInsertEntity($entity, Array $cols)
    {
        //If item exists, then we simply apply the ID to the entity
        if ($id = $this->checkItemExists($entity)) {
            $this->setId($entity, $id);
            return null;
        }
        else {
            $tableName = $this->getTableNameForEntity($entity);
            $this->dbal->insert($this->getTableNameForEntity($entity), $cols);
            $id = $this->dbal->lastInsertId();
            $this->setId($entity, $id);
            return true;
        }
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