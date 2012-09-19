<?php

namespace Bioteawebapi\Controllers;
use EasyRdf_Sparql_Client as SparqlClient;

class Front extends Abstracts\SparqlClientController
{
    public function run()
    {
        //For now, we'll just be a-testin
        return $this->test();
    }

    // --------------------------------------------------------------

    public function test()
    {
        $result = $this->sparqlQuery("select distinct ?Concept where {[] a ?Concept} LIMIT 100");
        echo $result->numRows();
        echo $result->dump();
    }

}

/* EOF: Front.php */