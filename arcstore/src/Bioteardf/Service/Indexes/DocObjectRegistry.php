<?php

namespace Bioteardf\Service\Indexes;

use Doctrine\ORM\EntityManager;
use Bioteardf\Model\Doc;

/**
 * Doc Object Registry that prevents duplicate objects from getting built
 *
 * TODO: LEFT OF HERE LEFT OFF HERE -- Test, test, test!! 
 */
class DocObjectRegistry
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var array
     */
    private $registry;

    // --------------------------------------------------------------

    /**
     * @param Doctrine\ORM\EntityManager
     */
    public function __construct(EntityManager $em = null)
    {
        $this->em       = $em;
        $this->registry = array();
    }

    // --------------------------------------------------------------

    /**
     * Dispense a new object
     *
     * Check existing graph for object, and also check db
     *
     * @param string $classBaseName
     * @param array $params
     * @return Bioteardf\Helper\BaseEntity
     */
    public function dispense($classBaseName, array $params = array())
    {
        $classBaseName = ucfirst($classBaseName);
        $className = "\\Bioteardf\\Model\\Doc\\" . $classBaseName;

        $reflection = new \ReflectionClass($className);
        $instance = $reflection->newInstanceArgs($params);

        $obj = $this->getExistingObject($className, $instance);

        //If there was an existing one, just get that.
        //Else, set the current one to persist
        if ($obj) {
            return $obj;
        }
        else {
            $this->em->persist($instance);
        }
    }

    // --------------------------------------------------------------

    /**
     * See if there is an existing object for this entity type
     *
     * @param  string $className
     * @param  Bioteardf\Helper\BaseEntity $instance
     * @return Bioteardf\Helper\BaseEntity
     */
    private function getExistingObject($className, $instance)
    {
        $uniqueId = (string) $instance;

        if (isset($this->registry[$className][$uniqueId])) {
            return $this->registery[$className][$uniqueId];
        }
        else {
            $repo = $this->em->getRepository($className);
            $obj =  $repo->findOneBy(array('locallyUniqueId' => $uniqueId));

            if ($obj) {
                $this->registry[$className][$uniqueId] = $obj;
            }

            return $obj;
        }
    }
}

/* EOF: DocObjectFactory.php */