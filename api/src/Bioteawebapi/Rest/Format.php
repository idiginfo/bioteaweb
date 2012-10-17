<?php

/**
 * Bioteaweb API
 *
 * A rest API frontend and indexer for the Biotea RDF project
 *
 * @link    http://biotea.idiginfo.org/api
 * @author  Casey McLaughlin <caseyamcl@gmail.com>
 * @license Copyright (c) Florida State University - All Rights Reserved
 */

// ------------------------------------------------------------------

namespace Bioteawebapi\Rest;

/**
 * MIME Format
 */
class Format
{
    /**
     * @var array
     */
    private $mimeTypes;

    /**
     * @var string
     */
    private $shortName;

    /**
     * @var string
     */
    private $description;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param array $mimeTypes  Array of mime-type strings associated with this format
     * @param string $shortName A short name (if null, no shortname = no query override)
     * @param string $description  An optional description of what this does
     */
    public function __construct($mimeTypes, $shortName = null, $description = null)
    {
        //If single mimeType sent..
        if ( ! is_array($mimeTypes)) {
            $mimeTypes = array($mimeTypes);
        }

        $this->mimeTypes = $mimeTypes;
        $this->shortName = $shortName;
        $this->description = $description;
    }

    // --------------------------------------------------------------

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }

    // --------------------------------------------------------------

    /**
     * @return array
     */
    public function getMimeTypes()
    {
        return $this->mimeTypes;
    }

    // --------------------------------------------------------------

    /**
     * The first mime-type is the one used by default
     *
     * @return string
     */
    public function getFirstMimeType()
    {
        $types = $this->mimeTypes;
        return array_shift($types);
    }

    // --------------------------------------------------------------

    /**
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }    

    // --------------------------------------------------------------

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}

/* EOF: Format.php */