<?php

namespace Bioteawebapi\Rest;
use Silex\Application as SilexApp;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller class handles negotiation, self describing, etc
 */
abstract class Controller
{
    const AUTO = 0;

    // --------------------------------------------------------------

    /**
     * @var Silex\Application
     */
    private $app;

    // --------------------------------------------------------------

    /**
     * @var Symfony\Component\HttpFoundation\Request
     */
    private $request;

    // --------------------------------------------------------------

    /**
     * @var array  Array of Format objects
     */
    private $acceptableFormats = array();

    // --------------------------------------------------------------

    /**
     * @var array  Array of Parameter objects (keys are parameter names) 
     */
    private $acceptableParameters = array();

    // --------------------------------------------------------------

    /**
     * @var array  Array of strings composed of available route names
     */
    private $routes;

    // --------------------------------------------------------------

    /**
     * Constructor
     */
    public function __construct(SilexApp $app)
    {
        $this->app     = $app;
        $this->request = $app['request'];

        //Run the configuration
        $this->configure();
    }

    // --------------------------------------------------------------

    /**
     * Build a summary of this object
     *
     * @return stdclass
     */
    public function getSummary()
    {
        $summary = array();
        $summary['route'] = 'GETROUTEPATHUSEDFROMREQUEST';

        //If there is a description...
        if ($routes[$summary['route']]) {
            $summary['description'] = $routes[$summary['route']];
        }
        else {
            $summary['description'] = 'No Description';
        }

        $summary['formats']     = array_map(function($v) { return $v->toArray(); }, $this->formats);
        $summary['parameters']  = array_map(function($v) { return $v->toArray(); }, $this->parameters);
        return (object) $summary;
    }

    // --------------------------------------------------------------

    public function run()
    {
        //LEFT OFF HERE LEFT OFF HERE!!!
        $args = func_get_args();

        $this->execute();
    }

    // --------------------------------------------------------------

    public function getRoutes()
    {
        return array_keys($this->routes);
    }

    // --------------------------------------------------------------

    /**
     * Configure method is called when the controller is run
     */
    abstract protected function configure();

    // --------------------------------------------------------------

    protected function check()
    {
        //Get all of the parmeters and check them against known parameters

        //Determine format
        $val = $this->request->query->get($paramName);
    }

    // --------------------------------------------------------------

    /**
     * Set routes that this controller corresponds to
     *
     * @param string $route
     * @param array|string|null    Array of methods, or string for single, or null for all
     * @param string $description  Optional description
     */
    protected function addRoute($route, $methods = null, $description = null)
    {
        $this->routes[$route] = new Route($route, $methods, $description);
    }

    // --------------------------------------------------------------

    /**
     * Set the description
     *
     * @param string $description
     */
    protected function setDescription($description)
    {
        $this->description = $description;
    }

    // --------------------------------------------------------------

    /**
     * Add an acceptable parameter
     *
     * @param string $name                 Parameter name corresponds to query string key
     * @param array|string $allowedValues  Can be an array of values or valid REGEX
     * @param string $description          Optional description
     */
    protected function addParameter($name, $allowedValues, $description = null)
    {
        $this->parameters[$name] = new Parmeter($name, $allowedValues, $description);
    }

    // --------------------------------------------------------------

    /**
     * Add an acceptable format
     *
     * @param array|string $mimeTypes    String for single, array for multiple
     * @param string       $shortName    Leave null to disallow query string override
     * @param string       $description  Optional description
     */
    protected function addFormat($mimeTypes, $shortName, $description = null)
    {
        //Check to see if the mimeType isn't already in-use
        $usedMimeTypes = array();
        foreach($this->formats as $fmt) {
            $usedMimeTypes = array_merge($usedMimeTypes, $fmt->getMimeTypes());
        }

        if (in_array($usedMimeTypes, $mimeTypes)) {
            throw new \Exception("Mime-type conflict.  You cannot assign a single mime type to two formats");
        }

        $this->formats[] = new Format($mimeTypes, $shortName, $description);
    }

    // --------------------------------------------------------------

    /**
     * Get Path Segment returns a path segment
     *
     * @param int $segment
     * @return string|null
     */
    protected function getPathSegment($segment) 
    {
        $pathInfo = $this->request->getPathInfo();
        $pathSegs = array_filter(explode("/", $pathInfo));

        return (isset($pathSegs[$segment])) ? $pathSegs[$segment] : null;
    }
}

/* EOF: Controller.php */