<?php

namespace Bioteaweb\Entities;

/**
 * Annotation Entity represents Indexes for a Biotea Annotation
 * 
 * @Entity
 */
class Annotation
{
    /** 
     * @var int
     * @Id @GeneratedValue @Column(type="integer") 
     **/
    protected $id;

    /**
     * @var Term
     * @OneToOne(targetEntity="Term")
     * @JoinColumn(name="term_id", referencedColumnName="id")
     **/
    protected $term;
}

/* EOF: Document.php */