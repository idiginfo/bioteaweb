<?php

namespace Bioteawebapi\Rest;

/**
 * A View Object can display itself a number of ways
 */
abstract class View
{
    /**
     * Convert the object to HTML
     *
     * For now, it just pretty-prints the JSON in <pre>...</pre> tags
     *
     * @return string
     */
    public function toHtml()
    {
        return sprintf("<pre class='item'>%s</pre>", $this->toJson());
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
        return $this->$val;
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