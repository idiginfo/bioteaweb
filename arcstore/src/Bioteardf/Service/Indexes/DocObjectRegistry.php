<?php

namespace Bioteardf\Service\Indexes;

use Doctrine\ORM\EntityManager;
use Bioteardf\Helper\DocIndexEntity;
use Bioteardf\Model\Doc;
use ArrayAccess, LogicException;

/**
 * Doc Object Registry that prevents duplicate objects from getting built
 */
class DocObjectRegistry implements ArrayAccess
{
    const DOC_NS = "\\Bioteardf\\Model\\Doc\\";

    // --------------------------------------------------------------

    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var array
     */
    private $registry;

    /**
     * @var Bioteardf\Model\Doc\Document
     */
    private $docObj;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Doctrine\ORM\EntityManager
     */
    public function __construct(EntityManager $em = null, $md5, $pmid = null)
    {
        $this->em       = $em;
        $this->registry = array();

        $className = self::DOC_NS . 'Document';
        $obj = new $className($md5, $pmid);

        $this->docObj = $this->checkForExisting($obj) ?: $obj;
    }

    // --------------------------------------------------------------

    public function offsetExists($offset)
    {
        $graph = $this->getGraph();
        return isset($graph[$offset]);
    }

    public function offsetGet($offset)
    {
        $graph = $this->getGraph();
        return $graph[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new LogicException("Cannot make state changes to object graph through array interface.  Use getObj()");
    }

    public function offsetUnset($offset)
    {
        throw new LogicException("Cannot make state changes to object graph through array interface.  Use getObj()");

    }

    // --------------------------------------------------------------

    /**
     * Return Document Object
     *
     * @return Bioteardf\Model\Doc\Document
     */
    public function getDocObj()
    {
        return $this->docObj;
    }

    // --------------------------------------------------------------

    /**
     * Dispense a new object
     *
     * Check existing graph for object, and also check db
     *
     * @param string $classBaseName
     * @param string|array $params
     * @return Bioteardf\Helper\DocIndexEntity
     */
    public function getObj($classBaseName, $params = array())
    {
        if ( ! is_array($params)) {
            $params = array($params);
        }

        $classBaseName = ucfirst($classBaseName);
        $fullClassName = self::DOC_NS . $classBaseName;

        //Create a new object
        $reflection = new \ReflectionClass($fullClassName);
        $instance = $reflection->newInstanceArgs($params);

        //Use the existing one, or if no existing one, then the new one.
        $obj = $this->checkForExisting($instance) ?: $instance;

        //Register it (if it isn't already registered)
        $this->register($obj);

        //Return it
        return $obj;
    }

    // --------------------------------------------------------------

    /**
     * Dump the Registry
     *
     * @return array
     */
    public function getGraph()
    {
        return array_merge(
            array('Document' => array($this->docObj)),
            $this->registry
        );
    }

    // --------------------------------------------------------------

    private function register(DocIndexEntity $instance)
    {
        //The unique ID is a string version of the instance
        $uniqueId  = (string) $instance;
        $shortName = join('', array_slice(explode('\\', get_class($instance)), -1));


        $this->registry[$shortName][$uniqueId] = $instance;
    }

    // --------------------------------------------------------------

    /**
     * See if there is an existing object for this entity type
     *
     * @param  Bioteardf\Helper\DocIndexEntity $instance
     * @return Bioteardf\Helper\DocIndexEntity|boolean  Returns false if not found
     */
    private function checkForExisting(DocIndexEntity $instance)
    {
        //The unique ID is a string version of the instance
        $uniqueId  = (string) $instance;
        $className = get_class($instance);
        $shortName = join('', array_slice(explode('\\', get_class($instance)), -1));

        //Check the registry
        if (isset($this->registry[$shortName][$uniqueId])) {
            return $this->registry[$shortName][$uniqueId];
        }
        else { //Check the DB
            $repo = $this->em->getRepository($className);
            $obj  = $repo->findOneBy(array('locallyUniqueId' => $uniqueId));
            return $obj;
        }
    }
}

/* EOF: DocObjectRegistry.php */