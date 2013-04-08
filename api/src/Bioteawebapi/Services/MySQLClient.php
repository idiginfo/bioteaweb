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

namespace Bioteawebapi\Services;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\EntityManager;
use Bioteawebapi\Entities\Annotation;
use Bioteawebapi\Entities\Document;
use Bioteawebapi\Entities\Term;
use Bioteawebapi\Entities\Topic;
use Bioteawebapi\Entities\Vocabulary;

/**
 * Read-only MySQL Client returns Entities from the database
 * and information about the database
 *
 * Some other services (such as Indexer) don't use this client, but
 * instead use the Doctrine\ORM and DBAL libraries directly, because
 * they update the database
 */
class MySQLClient
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var Doctrine\DBAL\Connection
     */
    private $dbal;

    /**
     * @var booelean  Internal flag for count queries
     */
    private $countOnly = false;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Doctrine\DBAL\Connection
     */
    public function __construct(EntityManager $em)
    {
        $this->em   = $em;
        $this->dbal = $this->em->getConnection();
    }

    // --------------------------------------------------------------

    /**
     * Count results from a query
     *
     * Wraps around any of the other public "get" methods from this class.
     * Will return a count of results rather than the actual results themselves.
     *
     * @param string    The method name to count results for
     * @param array|id  The first parameter you'd normally to send to the method
     * @param int       Offset
     * @param int       Limit
     * @return int      Count of records
     */
    public function count()
    {
        $args = func_get_args();

        $this->countOnly = true;

        try {
            $method = array_shift($args);
            $params = $args;
            $result = call_user_func_array(array($this, $method), $params);

            $this->countOnly = false;
            return $result;
        }
        catch (Exception $e) {
            $this->countOnly = false;
            throw $e;
        }
    }

    // --------------------------------------------------------------

    /**
     * Get Terms
     *
     * @param string $prefix  Optional prefix (generates LIKE="prefix%")
     * @param int $offset     Optional offset
     * @param int $limit      Optional limit
     */
    public function getTerms($prefix = null, $offset = 0, $limit = 0)
    {
        if ($prefix) {
            $qb = $this->em->createQueryBuilder();
            $where = $qb->expr()->like('e.term', $qb->expr()->literal($prefix . '%'));
        }
        else {
            $where = null;
        }

        return $this->runQuery("Bioteawebapi\Entities\Term", $where, $offset, $limit, 'term');
    }

    // --------------------------------------------------------------

    /**
     * Get Topics
     *
     * @param string $prefix  Optional prefix (generates LIKE="prefix%")
     * @param int $offset     Optional offset
     * @param int $limit      Optional limit
     */
    public function getTopics($id = null, $offset = 0, $limit = 0)
    {
        if ($id) {
            $qb = $this->em->createQueryBuilder();
            $where = $qb->expr()->eq('e.id', $qb->expr()->literal($id));
        }
        else {
            $where = null;
        }

        return $this->runQuery("Bioteawebapi\Entities\Topic", $where, $offset, $limit);
    }

    // --------------------------------------------------------------

    /**
     * Get Vocabularies
     *
     * @param string $prefix  Optional prefix (generates LIKE="prefix%")
     * @param int $offset     Optional offset
     * @param int $limit      Optional limit
     */
    public function getVocabularies($prefix = null, $offset = 0, $limit = 0)
    {
        if ($prefix) {
            $qb = $this->em->createQueryBuilder();
            $where = $qb->expr()->like('e.shortName', $qb->expr()->literal($prefix . '%'));
        }
        else {
            $where = null;
        }

        return $this->runQuery("Bioteawebapi\Entities\Vocabulary", $where, $offset, $limit, 'shortName');
    }

    // --------------------------------------------------------------

    /**
     * Get Documents
     *
     * @param string $path  Optional relative path
     * @param int $offset   Optional offset
     * @param int $limit    Optional limit
     */
    public function getDocuments($path = null, $offset = 0, $limit = 0)
    {
        if ($path) {
            $qb = $this->em->createQueryBuilder();
            $where = $qb->expr()->eq('e.rdfFilePath', $qb->expr()->literal($path));
        }
        else {
            $where = null;
        }

        return $this->runQuery("Bioteawebapi\Entities\Document", $where, $offset, $limit, 'rdfFilePath');
    }

    // --------------------------------------------------------------

    /**
     * Check Schema checks to see if the database schema is up-to-date
     *
     * @param boolean $returnQuries  If true, will return an array of queries
     * @return array|boolean
     */
    public function checkSchema($returnQueries = false)
    {
        $classes = array(
            $this->em->getClassMetadata('Bioteawebapi\Entities\Annotation'),
            $this->em->getClassMetadata('Bioteawebapi\Entities\Document'),
            $this->em->getClassMetadata('Bioteawebapi\Entities\Term'),
            $this->em->getClassMetadata('Bioteawebapi\Entities\Topic'),
            $this->em->getClassMetadata('Bioteawebapi\Entities\Vocabulary'),
        );

        $sm   = $this->dbal->getSchemaManager();
        $tool = new SchemaTool($this->em);
 
        //Get schemas
        $fromSchema = $sm->createSchema();
        $toSchema   = $tool->getSchemaFromMetadata($classes);

        //Get queries from comparator
        $comparator = new Comparator();
        $schemaDiff = $comparator->compare($fromSchema, $toSchema);
        $queries    = $schemaDiff->toSql($this->dbal->getDatabasePlatform());

        return ($returnQueries) ? $queries : (count($queries) == 0);
    }  

    // --------------------------------------------------------------

    /**
     * Get Documents
     *
     * @param string $path  Optional relative path
     * @param int $offset   Optional offset
     * @param int $limit    Optional limit
     */
    protected function runQuery($entityName, $where = null, $offset = 0, $limit = 0, $orderBy = 'id')
    {
        //Basic query builder
        $qb = $this->em->createQueryBuilder();
        $qb->add('from', $entityName . " e");

        if ($this->countOnly) {
            $qb->add('select', 'COUNT(e.id)');
        }
        else {
            $qb->add('select', 'e');
            $qb->add('orderBy', sprintf('e.%s ASC', $orderBy));
        }
           
        //Parameters
        if ($where) {
            $qb->add('where', $where);
        }

        //Offset and limit
        if ($offset) {
            $qb->setFirstResult($offset);
        }
        if ($limit) {
            $qb->setMaxResults($limit);
        }

        //Return query
        $query = $qb->getQuery();

        return ($this->countOnly) ? $query->getSingleScalarResult() : $query->getResult();
    }

}

/* EOF: MySQLClient.php */