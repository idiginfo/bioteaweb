<?php

namespace Bioteawebapi\Controllers;
use Bioteawebapi\Rest\Controller;
use Bioteawebapi\Rest\Format;
use Bioteawebapi\Rest\Route;
use Bioteawebapi\Rest\Parameter;
use Bioteawebapi\Views\PaginatedList;

/**
 * Single term info Controller
 */
class TermsSingle extends Controller
{
    protected function configure()
    {
        $this->add(new Route('/terms/{term}'));
        $this->add(new Format('text/html', 'html', "Returns information about a single term in HTML"));
        $this->add(new Format('application/json', 'json', "Returns information about a single term in JSON"));
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