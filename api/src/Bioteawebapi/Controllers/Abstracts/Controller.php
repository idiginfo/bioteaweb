<?php

namespace Bioteawebapi\Controllers\Abstracts;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    const AUTO = 0;

    // --------------------------------------------------------------

    /**
     * What format in which to return results
     */
    private $format = self::AUTO;

    // --------------------------------------------------------------

    /**
     * @var Silex\Application
     */
    protected $app;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Silex\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        //Register a 'before' to determine output format
        $this->app->before(array($this, 'determineFormat'));

        //Register error controller
        $this->app->error(array($this, 'handleError'));
    }

    // --------------------------------------------------------------

    /**
     * Run Abstract
     *
     * @return object|array  That can be encoded as JSON
     */
    public abstract function run();

    // --------------------------------------------------------------

    /**
     * Get Allowed Formats
     *
     * @return array  Array of formats ('json', 'csv', etc) in priority order
     */
    public abstract function getAllowedFormats();

    // --------------------------------------------------------------

    /**
     * Determine format based off the query string or request headers
     *
     * @return string
     * @throws \Exception
     */
    public function determineFormat()
    {
        //Key is shortname, value is mime-type
        $formatMap = array(
            'application/json' => 'json',
            'text/csv'         => 'csv',
            'text/html'        => 'html',
        );

        //Determine what formats are available for this URL
        $availFormats = $this->getAllowedFormats();

        //Consistency Check
        $diff = array_diff($availFormats, array_values($formatMap));
        if (count($diff) > 0) {
            throw new \Exception("The following format types do not exist: " . implode(', ', $diff));
        }

        //Convert the available formats to their mime types
        $availMimeTypes = array_map(function($v) use ($formatMap) {
            return array_search($v, $formatMap);
        }, $availFormats);

        //Was there a format specified in the query?
        $qFormat = $this->app['request']->query->get('format');
        if ($qFormat) {
            $requestedFormat = (array_search($qFormat, $formatMap)) ?: null;
        }
        else {
            $requestedFormat = $this->app['fosrest']->getBestFormat($this->app['request'], $availMimeTypes);
        }

        //Check it against the available types for this controller
        if ( ! in_array($requestedFormat, $availMimeTypes)) {

            $msg = sprintf(
                "Could not negotiate content type!  Available formats are: %s",
                ($qFormat) ? implode(', ', $availFormats) : implode(', ', $availMimeTypes)
            );

            $this->app->abort(415, $msg);
        }

        //Set it
        $this->app['format'] = $requestedFormat;

        //Return it
        return $requestedFormat;
    }

    // --------------------------------------------------------------

    /**
     * Error Handler
     *
     * Try to do so in the client's expected format
     *
     * @param \Exception $e
     * @param int $code
     */
    public function handleError(\Exception $e, $code)
    {
        if ( ! isset($this->app['format'])) {
            $this->app['format'] = null;
        }

        switch ($this->app['format']) {

            case 'text/html':
                return new Response($e->getMessage(), $code, array('content-type' => 'text/html'));
            break;
            case 'application/json':
                return $this->app->json(array('error' => $e->getMessage()), $code);
            break;
            case 'text/csv': default:
                return new Response($e->getMessage(), $code, array('content-type' => 'text/plain'));
            break;
        }

    }
}

/* EOF: Controller.php */