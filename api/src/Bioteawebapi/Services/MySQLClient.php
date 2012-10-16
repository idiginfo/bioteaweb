<?php

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

    public function getTerms($prefix = null, $offset = 0, $limit = 0)
    {
        if ($prefix) {
            $qb = $em->createQueryBuilder();
            $where = $qb->expr()->like('e.term', $qb->expr()->literal($prefix . '%'));
        }
        else {
            $where = null;
        }

        return $this->buildGetQuery("Bioteawebapi\Entities\Term", $where, $offset, $limit, 'term');
    }

    // --------------------------------------------------------------

    public function getTopics($prefix = null, $offset = 0, $limit = 0)
    {

    }

    // --------------------------------------------------------------

    public function getVocabularies($prefix = null, $offset = 0, $limit = 0)
    {

    }

    // --------------------------------------------------------------

    public function getDocuments($path = null, $offset = 0, $limit = 0)
    {

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

    protected function buildGetQuery($entityName, $where = null, $offset = 0, $limit = 0, $orderBy = 'id')
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
        return ($this->countOnly) ? $query->getSingleScalarResult() : $query->getArrayResult();
    }

}

/* EOF: MySQLClient.php */