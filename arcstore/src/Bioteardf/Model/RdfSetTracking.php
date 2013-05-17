<?php

namespace Bioteardf\Model;

use Bioteardf\Helper\BaseEntity;
use DateTime;

/**
 * Table to keep track fo where we've loaded RDF sets
 *
 * @Entity
 */
class RdfSetTracking extends BaseEntity
{
    /** 
     * @var int
     * @Id @GeneratedValue @Column(type="integer") 
     **/
    protected $id;

    /**
     * @var string  MD5 of BioteaRdfSet
     * @Column(type="string")
     */
    protected $md5;

    /**
     * @var string  Action taken (ie loaded into ARC2store, database, etc)
     * @Column(type="string")
     */
    protected $action;

    /**
     * @var DateTime
     * @Column(type="datetime")
     */
    protected $timestamp;

    // --------------------------------------------------------------

    public function __construct($md5, $action)
    {
        $this->md5       = $md5;
        $this->action    = $action;
        $this->timestamp = new DateTime();
    }
}

/* EOF: RdfSetTracking.php */