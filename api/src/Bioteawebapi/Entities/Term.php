<?php

namespace Bioteaweb\Entities;

/**
 * Annotation Entity represents Indexes for a Biotea Annotation
 * 
 * @Entity
 */
class Term
{
     /** @Id @GeneratedValue @Column(type="integer") */
    protected $id;

    /** @Column(type="string") **/
    protected $term;

    protected $topics;
}


/* EOF: Term.php */