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
 * A View Object can display itself a number of ways
 */
abstract class View
{
    /**
     * @var string
     */
    private $template;

    // --------------------------------------------------------------

    /**
     * Set the default template
     *
     * @param string $template  Include %content% placeholder somewhere in the string
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    // --------------------------------------------------------------

    /**
     * Convert the object to HTML
     *
     * For now, it just pretty-prints the JSON in <pre>...</pre> tags
     *
     * @param string $template  Optionally override the default template.
     *                          Use %content% for content placeholder.
     * @param string $content   Optioanlly, override the default content.
     * @return string
     */
    public function toHtml($template = null, $content = null)
    {
        if ( ! $template) {
            $template = $this->template;
        }

        if ( ! $content) {
            $content = sprintf("<pre id='json'>%s</pre>", $this->toJson());
        }

        //Replace the %content% placeholder with content
        $template = str_replace("%content%", $content, $template);

        return $template;
    }

    // --------------------------------------------------------------

    /**
     * Convert the object to JSON
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    // --------------------------------------------------------------

    /**
     * Magic method to get private/protected properties
     *
     * @param string $val
     * @return mixed
     */
    public function __get($val)
    {
        $arr = $this->toArray();
        return $arr[$val];
    }

    // --------------------------------------------------------------

    /**
     * Convert properties to array
     *
     * Uses reflection
     *
     * @return array
     */
    public function toArray()
    {
        $ref = new \ReflectionObject($this);
        $properties = $ref->getProperties();

        $arr = array();
        foreach($properties as $prop) {

            $prop->setAccessible(true);
            $arr[$prop->getName()] = $prop->getValue($this);
        }

        return $arr;
    }
}

/* EOF: View.php */