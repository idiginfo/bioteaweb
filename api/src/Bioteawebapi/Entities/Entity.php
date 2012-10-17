<?php

namespace Bioteawebapi\Entities;

abstract class Entity
{
    // --------------------------------------------------------------
    
    /**
     * Return an array representation of the entity
     *
     * @return array
     */
    public function toArray($includeId = false)
    {
        $ref = new \ReflectionObject($this);
        $properties = $ref->getProperties();

        $arr = array();
        foreach($properties as $prop) {

            $annots = $prop->getDocComment();
            if (stripos($annots, '@Column')) {

                if (stripos($annots, '@Id') && ! $includeId) {
                    continue;
                }

                $prop->setAccessible(true);
                $arr[$prop->getName()] = $prop->getValue($this);
            }            
        }

        return $arr;
    }

    // --------------------------------------------------------------

    /**
     * Each entity must implement toString()
     */
    abstract public function __toString();
}
/* EOF: Entity.php */