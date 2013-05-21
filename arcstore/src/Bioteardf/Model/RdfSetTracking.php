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
     * @var string  PMID of BioteaRdfSet
     * @Column(type="string")
     */
    protected $pmid;

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

    public function __construct($pmid, $action)
    {
        $this->pmid      = $pmid;
        $this->action    = $action;
        $this->timestamp = new DateTime();
    }
}

/* EOF: RdfSetTracking.php */