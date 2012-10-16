<?php

namespace Bioteawebapi\Controllers;
use Bioteawebapi\Rest\Controller;
use Bioteawebapi\Rest\Format;
use Bioteawebapi\Rest\Route;
use Bioteawebapi\Rest\Parameter;
use Bioteawebapi\Views\BasicView;

/**
 * Front Controller
 */
class Front extends Controller
{
    protected function configure()
    {
        $this->add(new Route('/', null, "Get information about the API"));
        $this->add(new Format('text/html', 'html', "HTML page showing information about the API"));
        $this->add(new Format('application/json', 'json', "JSON document containing information about the API"));
    }

    // --------------------------------------------------------------

    protected function execute()
    {
        switch($this->format) {

            case 'application/json':
                return $this->app->json($this->getSummary());
            case 'text/html': default:
                return $this->app->json($this->getSummary());
            break;
        }
    }
}

/* EOF: Front.php */