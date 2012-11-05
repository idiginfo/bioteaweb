<?php

/**
 * Bioteaweb API
 *
 * A rest API frontend and indexer for the Biotea RDF project
 *
 * @link    http://biotea.idiginfo.org/api
 * @author  Casey McLaughlin <caseyamcl@gmail.com>
 * @license Copyright (c) Florida State University - All Rights Reserved
 */

// ------------------------------------------------------------------

namespace Bioteawebapi\Controllers\Abstracts;
use Bioteawebapi\Rest\Format;
use Bioteawebapi\Rest\Route;
use Bioteawebapi\Rest\Parameter;
use Bioteawebapi\Views\PaginatedList;

/**
 * List Controller is for getting lists of entities in various ways
 */
abstract class ListController extends ApiController
{
    /**
     * @var int  Hardcoded items per page - - Can be abstracted
     */
    private $itemsPerPage = 100;

    // --------------------------------------------------------------

    protected function configure()
    {
        parent::configure();
        $this->add(new Parameter('page', '/^[\d]+$/', "Which page to retrieve for records that span multiple pages"));
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function getOutput()
    {
        //Call to itemCount
        $itemCount = $this->getItemCount();

        $page   = $this->getParameter('page') ?: 1;
        $offset = ($page == 1) ? 0 : ($page - 1) * $this->itemsPerPage;
        $limit  = $this->itemsPerPage;

        //Call to getItems
        $items = $this->getItems($offset, $limit);
        $items = $this->prepareResults($items);


        //Setup output view
        $output = $this->app['viewfactory']->build('PaginatedList', array($itemCount, $this->itemsPerPage));
        //$output = new PaginatedList($itemCount, $this->itemsPerPage);
        $output->setItems($items);
        $output->setOffset($offset);

        return $output;
    }

    // --------------------------------------------------------------

    /** @return int */
    protected abstract function getItemCount();

    /** @return array */
    protected abstract function getItems($offset, $limit);

    // --------------------------------------------------------------

    /**
     * Modify items performs any modifications to the items before returning
     * 
     * @param array $items
     * @return array
     */ 
    protected function prepareResults(Array $items)
    {
        //This doesn't do anything unless overridden
        return $items;
    }
}

/* EOF: ListController.php */