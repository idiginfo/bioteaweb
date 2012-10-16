<?php

namespace Bioteawebapi\Controllers;
use Bioteawebapi\Rest\Controller;
use Bioteawebapi\Rest\Format;
use Bioteawebapi\Rest\Route;
use Bioteawebapi\Rest\Parameter;
use Bioteawebapi\Views\PaginatedList; //  - Can be abstracted
use Doctrine\ORM\EntityManager; // - Can be abstracted

/**
 * Terms list controller
 */
class TermsList extends Controller
{
    /**
     * @var int  Hardcoded items per page - - Can be abstracted
     */
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
        //Call to itemCount - Can be abstracted
        $termCount = $this->getItemCount();

        $page   = $this->getParameter('page') ?: 1;
        $offset = ($page == 1) ? 0 : ($page - 1) * $this->itemsPerPage;
        $limit  = $this->itemsPerPage;

        //Call to getItems - Can be asbstracted
        $items = $this->getItems($offset, $limit);

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

    // --------------------------------------------------------------

    protected function getItemCount($prefix = null)
    {
        return $this->app['dbclient']->count('getTerms', $prefix);
    }

    // --------------------------------------------------------------

    protected function getItems($offset, $limit, $prefix = null)
    {
        return $this->app['dbclient']->getTerms($prefix, $offset, $limit);
    }
}

/* EOF: TermsList.php */