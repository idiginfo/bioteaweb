<?php

namespace Bioteawebapi\Services\Indexer;
use Doctrine\ORM\EntityManager;
use Bioteawebapi\Entities\Document;

/**
 * Index Persister class persists non-database-aware entity graphs
 *
 * This exists, because I could not get the Doctrine ORM to persist
 * the graph without errors all over the place, and because Doctrine
 * doesn't support UPSERT or REPLACE INTO.  Perhaps in D2.3 (final) or 2.4,
 * this functionality will exist, and we can remove this class.
 */
class IndexPersister
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var array  Pending inserts
     */

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
        $this->em = $em;

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

        //LEFT OFF HERE -- NOW I SEE HOW HARD THIS IS.. NOW WHAT?!
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

        //Get db metadata about the item through Doctrine method calls on it
        //what table?  What are the unique indexes that are set?
        $indexColumns = $this->getUniqueFieldsForEntity($entityName);

        //Build query parameters based off unique index columns
        $queryParams = array();
        foreach($indexColumns as $fieldName) {
            $fieldVal = $entity->$fieldName;

            if ($fieldVal) {
                $queryParams[$fieldName] = $fieldVal;
            }
        }

        //Does the query if we have anything to base it off of
        if (count($queryParams) > 0) {
            $rec = $this->em->getRepository($entityName)->findOneBy($queryParams);
        }

        //Return result
        return ($rec) ? $rec->id : $itemId;
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
        foreach ($metadata->table['uniqueConstraints'] as $idxName => $idxInfo) {
            foreach ($idxInfo['columns'] as $col) {
                $indexColumns[$col] = $metadata->fieldNames[$col];
            }
        }
        $this->cachedTableInfo[$entityName] = $indexColumns;
        return $indexColumns;
    }    
}

/* EOF: IndexPersister.php */