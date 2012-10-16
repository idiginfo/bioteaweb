<?php

namespace Bioteawebapi\Controllers;
use Bioteawebapi\Rest\Controller;
use Bioteawebapi\Rest\Format;
use Bioteawebapi\Rest\Route;
use Bioteawebapi\Rest\Parameter;
use Bioteawebapi\Views\PaginatedList;
use Doctrine\ORM\EntityManager;

/**
 * Single term info Controller
 */
class TermsSingle extends Controller
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    // --------------------------------------------------------------

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
    }
}

/* EOF: Front.php */