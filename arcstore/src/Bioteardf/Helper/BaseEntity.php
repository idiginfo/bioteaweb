<?php

namespace Bioteardf\Helper;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @MappedSuperclass
 */
abstract class BaseEntity
{
    /** 
     * @var int
     * @Id @GeneratedValue @Column(type="integer") 
     */
    protected $id;

    // --------------------------------------------------------------

    public function __get($item)
    {
        if ($item{0} != '_') {
            return $this->$item;    
        }
    }

    // --------------------------------------------------------------

    public function __isset($item)
    {
        return ($item{0} == '_') 
            ? false 
            : isset($this->$item);
    }

}

/* EOF: BaseEntity.php */