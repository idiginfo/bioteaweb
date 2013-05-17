<?php

namespace Bioteardf\Service;

use Doctrine\DBAL\Schema\Comparator;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Schema\Schema;

/**
 * Doctrine Entity Manager
 */
class DoctrineEntityManager 
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    // --------------------------------------------------------------

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    // --------------------------------------------------------------

    public function setup()
    {
        $queries = $this->schemaDiffs($this->getCurrentSchema(), $this->getOrmSchema());
        $dbal    = $this->em->getConnection();

        $dbal->transactional(function($dbal) use ($queries) {
            foreach($queries as $query) {
                $dbal->query($query);
            }
        });  

        return count($queries);      
    }

    // --------------------------------------------------------------

    public function isSetup()
    {
        $diffs = $this->schemaDiffs($this->getCurrentSchema(), $this->getOrmSchema());
        return (count($diffs) === 0);
    }

    // --------------------------------------------------------------

    public function reset()
    {
        $queries = $this->schemaDiffs($this->getCurrentSchema(), $this->getEmptySchema());
        $dbal    = $this->em->getConnection();

        $dbal->transactional(function($dbal) use ($queries) {
            foreach($queries as $query) {
                $dbal->query($query);
            }
        });  

        return count($queries);
    }

    // --------------------------------------------------------------

    protected function getOrmClassInfo()
    {
        $classes = array(
            $this->em->getClassMetadata('Bioteardf\Model\BioteaRdfSet'),
            $this->em->getClassMetadata('Bioteardf\Model\RdfSetTracking'),            
            $this->em->getClassMetadata('Bioteardf\Model\Doc\Annotation'),
            $this->em->getClassMetadata('Bioteardf\Model\Doc\Document'),
            $this->em->getClassMetadata('Bioteardf\Model\Doc\Journal'),
            $this->em->getClassMetadata('Bioteardf\Model\Doc\Paragraph'),
            $this->em->getClassMetadata('Bioteardf\Model\Doc\Term'),
            $this->em->getClassMetadata('Bioteardf\Model\Doc\TermInstance'),
            $this->em->getClassMetadata('Bioteardf\Model\Doc\Topic'),
            $this->em->getClassMetadata('Bioteardf\Model\Doc\Vocabulary')
        );

        return $classes;        
    }

    // --------------------------------------------------------------

    protected function getCurrentSchema()
    {
        $sm = $this->em->getConnection()->getSchemaManager();
        return $sm->createSchema();
    }

    // --------------------------------------------------------------

    protected function getOrmSchema()
    {
        $classes = $this->getOrmClassInfo();
        $sTool   = new SchemaTool($this->em);
        return $sTool->getSchemaFromMetadata($classes);
    }

    // --------------------------------------------------------------

    protected function getEmptySchema()
    {
        return new Schema();
    }

    // --------------------------------------------------------------

    /**
     * @return array  Array of queries to run
     */
    protected function schemaDiffs($fromSchema, $toSchema)
    {
        //Get queries from comparator
        $comparator = new Comparator();
        $schemaDiff = $comparator->compare($fromSchema, $toSchema);
        $queries    = $schemaDiff->toSql($this->em->getConnection()->getDatabasePlatform());

        return $queries;
    }
}

/* EOF: DoctrineEntityManager.php */