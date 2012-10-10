<?php

namespace Bioteawebapi\Rest;

/**
 * Silex Route with extra info
 */
class Route
{
    private $route;

    private $methods;

    private $description;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param string $route          Silex-compatible route
     * @param array|string $methods  String for single method, array for multiple, null for all
     * @param string $description    Optional description
     */
    public function __construct($route, $methods = null, $description = null)
    {
        $availMethods = array('get', 'post', 'put', 'delete');

        $this->route = $route;

        if ( ! is_null($methods)) {
            $this->methods = (is_array($methods)) ? $methods : array($methods);
        }
        else {
            $this->methods = $availMethods;
        }

        //Check for invalid methods
        $diff = array_diff(array_map('strtolower', $this->methods), $availMethods));
        if (count($diff) > 0) {
            throw new \Exception("Invalid methods configured in Controller: " . implode(', ', $diff));
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
        return get_object_vars($this);
    }

    // --------------------------------------------------------------

    public function getRoute()
    {
        return $this->route;
    }

    // --------------------------------------------------------------

    /**
     * Get HTTP methods (verbs)
     *
     * @param boolean $asString  If TRUE, returns a string with PIPE delimeters
     * @return array|string
     */
    public function getMethods($asString = false)
    {
        return ($asString) ? strtoupper(implode('|', $this->methods)) : $this->methods;
    }

    // --------------------------------------------------------------

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }}

/* EOF: Route.php */