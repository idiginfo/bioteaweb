<?php

namespace Bioteardf\Helper;

/**
 * @MappedSuperclass
 */
abstract class DocIndexEntity extends BaseEntity
{
    /**
     * @var string
     * @column(type="string", unique=true, nullable=false)
     */
    protected $locallyUniqueId;

    // --------------------------------------------------------------

    /**
     * This should always return a locally unique identifier
     *
     * @return string
     */
    abstract public function __tostring();
}

/* EOF: IndexEntity.php */