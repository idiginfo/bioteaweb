<?php

namespace Bioteawebapi\Rest;
use Silex\Application as SilexApp;
use Symfony\Component\HttpFoundation\Response;

class Application
{
    /**
     * @var array  Array of Controller objects
     */
    private $controllers;

    // --------------------------------------------------------------

    /**
     * @param Silex\Application
     */
    private $silexApp;

    // --------------------------------------------------------------

    public function __construct(SilexApp $app)
    {
        $this->silexApp = $app;

        //Register error controller
        $this->silexApp->error(array($this, 'handleError'));        
    }

    // --------------------------------------------------------------

    /**
     * Add a Controller to the application and register its routes
     * 
     * @param Controller $controller
     */
    public function add(Controller $controller)
    {
        //Inject the SilexApp into the controller
        $controller->configure();

        //Register routes with Silex
        foreach($controller->getRoutes() as $route) {

            $this->silexApp->match(
                $route->getRoute(), array($controller, 'run')
            )->method($route->getMethods(true));
        }     

        //Add the controller
        $this->controllers[] = $controller;

    }

    // --------------------------------------------------------------

    /**
     * Run the application
     */
    public function run()
    {
        //Determine route

        //Run that controller's execute method
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
        if ( ! isset($this->silexApp['format'])) {
            $this->silexApp['format'] = null;
        }

        switch ($this->silexApp['format']) {

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

/* EOF: Application.php */