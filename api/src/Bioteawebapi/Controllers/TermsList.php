<?php

namespace Bioteawebapi\Controllers;
use Bioteawebapi\Rest\Controller;
use Bioteawebapi\Rest\Format;
use Bioteawebapi\Rest\Route;
use Bioteawebapi\Rest\Parameter;
use Bioteawebapi\Views\PaginatedList;

/**
 * Terms list controller
 */
class TermsList extends Controller
{
    private $itemsPerPage = 100;

    // --------------------------------------------------------------

    protected function configure()
    {
        $this->add(new Route('/terms'));
        $this->add(new Format('text/html', 'html', "HTML page showing information about the API"));
        $this->add(new Format('application/json', 'json', "JSON document containing information about the API"));
        $this->add(new Parameter('page', '/^[\d]+$/', "Which page to retrieve for records that span multiple pages"));
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function execute()
    {
        //@TODO: FIX THIS! Abstract it or something - It's terrible code
        $termCount = (int) $this->app['db']->query("SELECT count(`id`) FROM `terms`")->fetchColumn(0);
        
        $page   = $this->getParameter('page') ?: 1;
        $offset = ($page == 1) ? 0 : ($page - 1) * $this->itemsPerPage;
        $limit  = $this->itemsPerPage;

        //@TODO: FIX THIS! Abstract it or something - It's terrible code
        $items  = $this->app['db']->query("SELECT * FROM `terms` LIMIT $limit OFFSET $offset")->fetchAll();

        //Setup output view
        $output = new PaginatedList($termCount, $this->itemsPerPage);
        $output->setItems($items);
        $output->setOffset($offset);

        //Output it!
        switch($this->format) {
            case 'text/html':
                return $output->toHtml();
            case 'application/json':
                return $output->toJson();
        }
    }
}

/* EOF: TermsList.php */