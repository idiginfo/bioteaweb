<?php

namespace Bioteawebapi\Services\Indexer;
use Doctrine\ORM\EntityManager;

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

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
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
        //If the document already exists, just return false
        if ($this->checkItemExists($document)) {
            return false;
        }

        //Else, go through an index the whole thing
    }

    // --------------------------------------------------------------



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

        //Get db metadata about the item through Doctrine method calls on it
        //what table?  What are the unique indexes that are set?

        //Query the collection to see if this exists.  If so, return the unique ID
        //for that item

        //ELse return null

        //This is just for reference; delete it
        reuturn null OR $itemId;
    }    
}

/* EOF: IndexPersister.php */