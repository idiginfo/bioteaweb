<?php

namespace Bioteawebapi\Controllers;
use Bioteawebapi\Services\SolrClient;
use Silex\Application;

class Terms extends Controller
{
    private $solrClient;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Bioteawebapi\Services\SolrClient $solrClient
     */
    public function __construct(Application $app, SolrClient $solrClient)
    {
        parent::__construct($app);
        
        $this->solrClient = $solrClient;
    }

    // --------------------------------------------------------------

    public function run()
    {
        //Which field are we getting?
        $field = $this->getPathSegment(1);

        //First, we try to get the field from the solr index
        $terms = $this->app['solr_client']->getTerms($field);

        //Build an output object and send it back! LEFT OFF HERE LEFT OFF HERE
        var_dump($terms);
    }

    // --------------------------------------------------------------

    public function getAllowedFormats()
    {
        //Maybe add more later
        return array('json', 'csv', 'xml');
    }
}

/* EOF: SolrQuery.php */