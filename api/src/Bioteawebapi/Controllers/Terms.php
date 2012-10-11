<?php

namespace Bioteawebapi\Controllers;
use Bioteawebapi\Rest\Controller;

/**
 * Front Controller
 */
class Terms extends Controller
{
    protected function configure()
    {
        $this->addRoute('/terms/{term}');
        $this->addFormat('text/html', 'html', "HTML page showing information about the API");
        $this->addFormat('application/json', 'json', "JSON document containing information about the API");
    }

    // --------------------------------------------------------------

    protected function execute()
    {
        //Get information about a specific term
        $term = $this->getPathSegment(2);

        //Do a DB Query -- @TODO: Abstract this out!
        //TRY 'cancer'
        $result = $this->app['db']->executeQuery("SELECT * FROM terms WHERE term = '$term';");
        var_dump($result->fetch());

    }
}

/* EOF: Front.php */