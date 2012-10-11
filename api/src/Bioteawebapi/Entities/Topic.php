<?php

namespace Bioteaweb\Entities;

/**
 * Vocabulary Entity
 * 
 * @Entity
 */
class Vocabulary
{
    /** @Id @GeneratedValue @Column(type="integer") **/
    protected $id;

    /** @Column(type="string") **/
    protected $uri;

    /** @Column(type="string") **/
    protected $shortName; 

    /**
     * @OneToOne(targetEntity="Topic", mappedBy="vocabulary")
     **/
    protected $topic;   
}

/* EOF: Topic.php */