<?php

namespace Bioteawebapi\Controllers;

/**
 * URL is: /terms
 */
class Terms extends Abstracts\SparqlClientController
{
    public function run($term = null)
    {
        echo "TERM IS: $term";
    }
}

/* EOF: Terms.php */