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
     * @var array  Array of Format objects
     */
    private $acceptableFormats = array();

    /**
     * @var array  Array of Parameter objects (keys are parameter names) 
     */
    private $acceptableParameters = array();

    /**
     * @var array  Array of strings composed of available route names
     */
    private $routes;

    /**
     * @var string  Array of key/values for parameters passed in
     */
    private $parameters;

    /**
     * @var Silex\Application
     */
    protected $app;

    /**
     * @var string  The negotiated format
     */
    protected $format;

    // --------------------------------------------------------------

    /**
     * Constructor
     */
    public function __construct(SilexApp $app)
    {
        $this->app = $app;

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

        //Route
        $summary['route'] = $this->app['request']->getPathInfo();

        //If there is a description...
        if (isset($this->routes[$summary['route']])) {
            $summary['description'] = $this->routes[$summary['route']];
        }
        else {
            $summary['description'] = 'No Description';
        }

        //Get formats
        $summary['formats'] = array_map(function($v) { 
            return $v->toArray();
        }, $this->acceptableFormats);

        //Get parameters
        $summary['parameters'] = array_map(function($v) {
            return $v->toArray();
        }, $this->acceptableParameters);

        //Return it
        return (object) $summary;
    }

    // --------------------------------------------------------------

    /**
     * Run it
     */
    public function run()
    {
        //Check the parameters and the formats
        $this->check();

        return call_user_func(array($this, 'execute'));
    }

    // --------------------------------------------------------------

    public function getRoutes()
    {
        return $this->routes;
    }

    // --------------------------------------------------------------

    /**
     * Returns a parameter, if set
     *
     * @param string $parameter
     * @return string
     */
    public function getParameter($parameter)
    {
        return (isset($this->parameters[$parameter]))
            ? $this->parameters[$parameter]
            : null;
    }

    // --------------------------------------------------------------

    /**
     * Configure method is called when the controller is instantiated
     */
    abstract protected function configure();

    // --------------------------------------------------------------

    /**
     * Execute method is called when the controller is run
     */
    abstract protected function execute();

    // --------------------------------------------------------------

    protected function check()
    {
        //Get all of the parmeters and check them against known parameters
        foreach($this->app['request']->query as $key => $val) {
            if (isset($this->acceptableParameters[$key])) {
                try {

                    $this->acceptableParameters[$key]->checkValue($val);
                    $this->parameters[$key] = $val;

                } catch (\InvalidArgumentException $e) {
                    $this->app->abort(400, $e->getMessage());
                }
            }
        }

        //Acceptable formats
        if (count($this->acceptableFormats) == 0) {
            $this->app->abort(415, "No known formats available.");
        }

        //Negotiate using ?format=xx in query string if set
        if ($reqFormat = $this->app['request']->query->get('format')) {

            $shortNames = array();
            foreach($this->acceptableFormats as $fmt) {
                $shortNames[$fmt->getShortName()] = $fmt;
            }

            if (in_array(strtolower($reqFormat), array_keys($shortNames))) {
                $this->format = $shortNames[$reqFormat]->getFirstMimeType();
            }
            else {
                $this->app->abort(415, sprintf(
                    "Format %s is invalid.  Available formats are: %s",
                    $reqFormat, implode(', ', array_keys($shortNames))
                ));
            }
        }
        else { //else, negotiate using FOSREST (content-type)

            $availMimeTypes = array();
            foreach($this->acceptableFormats as $fmt) {
                $availMimeTypes = array_merge($availMimeTypes, $fmt->getMimeTypes());
            }

            $result = $this->app['fosrest']->getBestFormat($this->app['request'], $availMimeTypes);

            if ($result) {
                $this->format = $result;
            }
            else {
                $this->app->abort(415, sprintf(
                    "Could not negotiate format!  Available formats are: %s",
                    implode("; ", $availMimeTypes)
                ));
            }
        }

        //Also inform the App for use outside the controller
        $this->app['format'] = $this->format;
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
     * Add an acceptable parameter
     *
     * @param string $name                 Parameter name corresponds to query string key
     * @param array|string $allowedValues  Can be an array of values or valid REGEX
     * @param string $description          Optional description
     */
    protected function addParameter($name, $allowedValues, $description = null)
    {
        if ($name == 'format') {
            throw new \InvalidArgumentException("'format' is a reserved parameter name");
        }

        $this->acceptableParameters[$name] = new Parameter($name, $allowedValues, $description);
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
        //Set to array
        $mimeTypes = (is_array($mimeTypes)) ? $mimeTypes : array($mimeTypes);

        //Check to see if the mimeType isn't already in-use
        $usedMimeTypes = array();
        foreach($this->acceptableFormats as $fmt) {
            $usedMimeTypes = array_merge($usedMimeTypes, $fmt->getMimeTypes());
        }

        if (count(array_diff($mimeTypes, $usedMimeTypes)) < 1) {
            throw new \Exception("Mime-type conflict.  You cannot assign a single mime type to two formats");
        }

        $this->acceptableFormats[] = new Format($mimeTypes, $shortName, $description);
    }

    // --------------------------------------------------------------

    /**
     * Shortcut to get the request path
     *
     * @return string
     */
    protected function getPath()
    {
        return $this->app['request']->getPathInfo();
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
        $pathInfo = $this->app['request']->getPathInfo();
        $pathSegs = array_filter(explode("/", $pathInfo));

        return (isset($pathSegs[$segment])) ? $pathSegs[$segment] : null;
    }
}

/* EOF: Controller.php */