<?php

namespace Bioteardf\Helper;

/**
 * @MappedSuperclass
 * @HasLifecycleCallbacks
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
     * Set the locallyUniqueId
     *
     * @PrePersist
     */
    public function setLocallyUniqueId()
    {
        $this->locallyUniqueId = (string) $this;
    }

    // --------------------------------------------------------------

    /**
     * This should always return a locally unique identifier
     *
     * @return string
     */
    abstract public function __tostring();
}

/* EOF: IndexEntity.php */