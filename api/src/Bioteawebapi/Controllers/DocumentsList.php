<?php

namespace Bioteawebapi\Controllers;
use Bioteawebapi\Rest\Parameter;

class DocumentsList extends Abstracts\ListController
{
    /** @inherit */
    protected function configure()
    {
        parent::configure();
    }    

    // --------------------------------------------------------------

    protected function assignRoutes()
    {
        return array('/documents' => "Get a list of document URLs");
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignJsonDesc()
    {
        return "List documents in JSON format";
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function assignHtmlDesc()
    {
        return "List documents in HTML format";
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function getItemCount()
    {
        return $this->app['dbclient']->count('getDocuments');
    }

    // --------------------------------------------------------------

    /** @inherit */
    protected function getItems($offset, $limit)
    {
        return $this->app['dbclient']->getDocuments($offset, $limit);
    }

    // --------------------------------------------------------------
}

/* EOF: DocumentsList.php */