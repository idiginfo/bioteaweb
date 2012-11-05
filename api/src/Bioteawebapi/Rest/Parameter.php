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
 * Query Parameter
 */
class Parameter
{
    /**
     * @var string  Parameter name (key)
     */
    private $name;

    /**
     * @var string|array  Array of values or regexp
     */
    private $allowedValues;

    /**
     * @var string
     */
    private $description;

    /**
     * @var mixed
     */
    private $default;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param string       $name
     * @param string|array $allowedValues  Either regex or an array of accepatable values
     * @param string       $description    Optional description
     */
    public function __construct($name, $allowedValues, $description = null, $default = null)
    {
        if ($name == 'format') {
            throw new \InvalidArgumentException("'format' is a reserved parameter name");
        }
        
        $this->name = $name;
        $this->allowedValues = $allowedValues;
        $this->description = $description;

        if ($default) {
            $this->default = $default;
        }
    }

    // --------------------------------------------------------------

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray()
    {
        $arr = get_object_vars($this);
        if (is_null($arr['default'])) {
            unset($arr['default']);
        }

        return $arr;
    }
    
    // --------------------------------------------------------------

    /**
     * Get the parameter name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    // --------------------------------------------------------------

    /**
     * Get allowed values arary or regex
     *
     * @return string|array  Regex or array
     */
    public function getAllowedValues()
    {
        return $this->allowedValues;
    }

    // --------------------------------------------------------------

    /**
     * Check if a value submitted is valid
     *
     * @param string $value
     * @param boolean $exception
     * @return boolean
     * @throws InvalidArgumentException  If $excpetion is true
     */
    public function checkValue($value, $exception = true)
    {
        $allowedValues = $this->allowedValues;

        if (is_array($allowedValues)) {

            if ( ! in_array($value, $allowedValues)) {

                $fail = sprintf("%s is not an acceptable parameter for %s.  Allowed values are: %s",
                    $value, $this->getName(), implode(", ", $allowedValues)
                );
            }
        }
        elseif (is_string($allowedValues) && $allowedValues{0} = "/") {

            if ( ! preg_match($allowedValues, $value)) {
                $fail = sprintf(
                    "%s is not an acceptable parameter for %s.",
                    $value, $this->getName()
                );
            }
        }

        if ($exception && isset($fail)) {
            throw new \InvalidArgumentException($fail);
        }   
        else {
            return (isset($fail)) ? false : true;
        }   
    }

    // --------------------------------------------------------------

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    // --------------------------------------------------------------

    /**
     * @return mixed|null  NULL if no default
     */
    public function getDefault()
    {
        return $this->default ?: null;
    }    
}

/* EOF: Parameter.php */