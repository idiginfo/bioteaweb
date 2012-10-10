<?php

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
        $this->shortname = $shortName;
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