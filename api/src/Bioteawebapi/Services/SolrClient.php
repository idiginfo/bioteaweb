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

namespace Bioteawebapi\Services;
use Solarium_Client as SolariumClient;
use Bioteawebapi\Models\SolrIndexDocument as Document;

class SolrClient
{
    /**
     * @var \Solarium_Client
     */
    private $client;

    /**
     * @var \Solarium_Query
     */
    private $update = null;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param \Solarium_Client $client
     */
    public function __construct(SolariumClient $solariumClient)
    {
        $this->client = $solariumClient;
    }

    // --------------------------------------------------------------

    /**
     * Send an arbitrary query string using SOLR syntax
     *
     * @param string $queryString           A SOLR-compliant Query string
     * @param int $offset                   Defaults to 0
     * @param int $limit                    Defaults to 100
     * @return \Solarium_Result_Select|int  An iterable result or integer
     */
    public function query($queryString, $offset = 0, $limit = 100)
    {
        $query = $this->client->createSelect();
        $query->setQuery($queryString);

        $result = $this->client->select($query);
        return $result;
    }

    // --------------------------------------------------------------

    /**
     * Query SOLR
     *
     * @param array $queryParams            An array of key/value query params
     * @param int $offset                   Defaults to 0
     * @param int $limit                    Defaults to 100     
     * @param string $boolean               'AND' or 'OR'
     * @return \Solarium_Result_Select|int  An iterable result or integer
     */
    public function search($queryParams = array(), $offset = 0, $limit = 100, $boolean = 'AND')
    {
        if ( ! in_array(strtoupper($boolean), array('AND', 'OR'))) {
            throw new \InvalidArgumentException("");
        }

        $queryString = array();

        foreach($queryParams as $param => $value) {
            $queryString[] = sprintf("%s:'%s'", $param, $value);
        }
        $queryString = (count($queryString) > 0)
            ? implode(" {$boolean} ", $queryString) : '*:*';

        return $this->query($queryString);
    }

    // --------------------------------------------------------------

    /**
     * Insert a document into SOLR index (not yet committed)
     *
     * @param \Bioteawebapi\Models\SolrIndexDocument|array $docs  A single document or array of them
     * @return int  The number added
     */
    public function update($docs)
    {
        $docs = ( ! is_array($docs))
            ? array($docs) : $docs;

        //Assert type
        array_map(function($doc) { assert($doc instanceof Document); }, $docs);

        //Create an update
        if (is_null($this->update)) {
            $this->update = $this->client->createUpdate();
        }

        //Add the documents
        foreach($docs as $doc) {
            $updateDoc = $this->update->createDocument($doc->params());
            $this->update->addDocuments(array($updateDoc));
        }

        return count($docs);
    }

    // --------------------------------------------------------------

    /**
     * Commit updates to SOLR server
     *
     * @return int  The number of items updated
     */
    public function commit()
    {
        if ( ! is_null($this->update)) {

            //Commit the update
            $this->update->addCommit();
            $result = $this->client->update($this->update);

            //Return the count
            $count = count($this->update->getCommands()) - 1;

            //Reset the update property
            $this->update = null;

        }
        else {
            $count = 0;
        }

        return $count;
    }   

    // --------------------------------------------------------------

    /**
     * Get terms list
     *
     * @return array  Keys are terms, values are number of hits
     */
    public function getTerms($field, $limit = 100, $lower = null)
    {
        $termsQuery = $this->client->createTerms();
        $termsQuery->setFields($field);
        $termsQuery->setLimit($limit);
        
        if ($lower) {
            $termsQuery->setLowerbound($lower);
            $TermsQUery->setLowerboundinclude(false);
        }

        $result = $this->client->terms($termsQuery);
        $result = $result->getData();
        if (isset($result['terms'][1])) {
            $termList = $result['terms'][1];

            $outArr = array();

            for ($i = 0; $i < count($termList); $i += 2) {
                list($term, $count) = array($termList[$i], $termList[$i+1]);
                $outArr[$term] = $count;
            }

            return $outArr;
        }
        else {
            return array();
        }

    }

    // --------------------------------------------------------------

    /**
     * Clear the SOLR index
     *
     * @return boolean
     */
    public function clearIndex()
    {
        $update = $this->client->createUpdate();
        $update->addDeleteQuery('*:*');
        $update->addCommit();
        $result = $this->client->update($update);

        return ($result) ? true : false;
    }    
}

/* EOF: SolrIndexDocumentManager.php */