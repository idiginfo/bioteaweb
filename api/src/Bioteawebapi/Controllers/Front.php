<?php

namespace Bioteawebapi\Controllers;
use EasyRdf_Sparql_Client as SparqlClient;

/**
 * URL is: /
 */
class Front extends Abstracts\SparqlClientController
{
    // --------------------------------------------------------------

    public function run()
    {
        //For now, we'll just be a-testin
        return $this->test();
    }

    // --------------------------------------------------------------

    public function test()
    {
        echo "Oh Hai";
    }

}

/* EOF: Front.php */