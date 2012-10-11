<?php

namespace Bioteawebapi\Controllers;
use Bioteawebapi\Rest\Controller;

/**
 * Single term info Controller
 */
class TermsSingle extends Controller
{
    protected function configure()
    {
        $this->addRoute('/terms/{term}');
        $this->addFormat('text/html', 'html', "Returns information about a single term in HTML");
        $this->addFormat('application/json', 'json', "Returns information about a single term in JSON");
    }

    // --------------------------------------------------------------

    protected function execute()
    {
        //Get information about a specific term
        $term = urldecode($this->getPathSegment(2));


        //Do a DB Query -- @TODO: Abstract this out!
        //TRY 'cancer'
        $result = $this->app['db']->executeQuery("SELECT * FROM terms WHERE term = '$term';");
        var_dump($result->fetch());
    }
}

/* EOF: Front.php */