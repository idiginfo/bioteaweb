<?php

namespace Bioteawebapi\Services;
use Solarium_Client as SolrClient;
use Bioteawebapi\Models\SolrIndexDocument as Document;

class SolrIndexDocumentManager
{
    private $client;

    private $update = null;

    // --------------------------------------------------------------

    public function __construct(SolrClient $client)
    {
        $this->client = $client;
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
        return ($countOnly) ? $result->getNumFound() : $result;
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

        return $this->query($queryString, $countOnly);
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