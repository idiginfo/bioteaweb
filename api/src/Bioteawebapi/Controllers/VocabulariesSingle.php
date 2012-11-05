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

namespace Bioteawebapi\Controllers;
use Bioteawebapi\Rest\Format;
use Bioteawebapi\Rest\Route;
use Bioteawebapi\Rest\Parameter;

/**
 * Single vocabulary info Controller
 */
class VocabulariesSingle extends Abstracts\SingleEntityController
{
    /** @inherit */
    protected function configure()
    {
        $this->add(new Route('/vocabularies/{vocabulary}'));
        $this->add(new Format('text/html', 'html', "Returns information about a single vocabulary in HTML"));
        $this->add(new Format('application/json', 'json', "Returns information about a single vocabulary in JSON"));
    }


    // --------------------------------------------------------------

    /** @inherit */
    protected function execute()
    {
    }    
}

/* EOF: VocabulariesSingle.php */