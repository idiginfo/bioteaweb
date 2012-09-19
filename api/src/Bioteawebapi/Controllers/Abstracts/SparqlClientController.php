<?php

namespace Bioteawebapi\Controllers\Abstracts;

use Silex\Application;
use EasyRdf_Sparql_Client as SparqlClient;

abstract class SparqlClientController extends Controller
{
    /**
     * @var EasyRdf_Sparql_Client
     */
    private $sparqlClient;

    /**
     * @var boolean
     */
    private $countOnly = false;

    /**
     * @var string
     */
    private $prefix = null;

    /**
     * @var int
     */
    private $page = 1;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Silex\Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->sparqlClient = $app['sparql_client'];
    }

    // --------------------------------------------------------------

    public function getAllowedFormats()
    {
        return array('json', 'csv', 'html');
    }

    // --------------------------------------------------------------

    /**
     * Run a SPARQL Query
     *
     * @param string $query
     * @return \EasyRdf_Sparql_Result
     */
    protected function sparqlQuery($query)
    {
        $result = $this->sparqlClient->query($query);
        return $result;
    }
}

/* EOF: SparqlClientController.php */