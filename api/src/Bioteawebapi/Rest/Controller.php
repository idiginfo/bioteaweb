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
        if ($this->routes[$summary['route']]->getDescription()) {
            $summary['description'] = $this->routes[$summary['route']]->getDescription();
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
     * Add a route, parameter, or format
     *
     * @param Route|Parameter|Format $param
     * @throws \InvalidArgumentException If invalid $param
     */
    protected function add($param)
    {
        if ($param instanceOf Route) {
            $this->addRoute($param);
        }
        elseif ($param instanceOf Format) {
            $this->addFormat($param);
        }
        elseif ($param instanceOf Parameter) {
            $this->addParameter($param);
        }
        else {
            throw new \InvalidArgumentException("The value sent must be an instance of Route, Parameter, or Format");
        }
    }

    // --------------------------------------------------------------

    /**
     * Set routes that this controller corresponds to
     *
     * @param Route $route
     */
    protected function addRoute(Route $route)
    {
        $this->routes[$route->getRoute()] = $route;
    }

    // --------------------------------------------------------------

    /**
     * Add an acceptable parameter
     *
     * @param Parameter $parameter
     */
    protected function addParameter(Parameter $parameter)
    {
        $this->acceptableParameters[$parameter->getName()] = $parameter;
    }

    // --------------------------------------------------------------

    /**
     * Add an acceptable format
     *
     * @param Format $format
     */
    protected function addFormat($format)
    {
        $mimeTypes = $format->getMimeTypes();

        //Check to see if the mimeType isn't already in-use
        $usedMimeTypes = array();
        foreach($this->acceptableFormats as $fmt) {
            $usedMimeTypes = array_merge($usedMimeTypes, $fmt->getMimeTypes());
        }

        if (count(array_diff($mimeTypes, $usedMimeTypes)) < 1) {
            throw new \Exception("Mime-type conflict.  You cannot assign a single mime type to two formats");
        }

        $this->acceptableFormats[] = $format;
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