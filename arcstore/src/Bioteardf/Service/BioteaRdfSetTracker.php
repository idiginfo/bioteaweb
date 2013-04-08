<?php

/**
 * Mapper interface to the database to track RDF files
 * to keep track of the state of RDF loading
 */
class BioteaRdfSetTracker
{
    /**
     * @var octrine\DBAL\Connection
     */
    private $conn;

    /**
     * Constructor
     *
     * @param Doctrine\DBAL\Connection
     */
    public function __constrcut(Connection $conn)
    {
        $this->conn = $conn;
    }

    // --------------------------------------------------------------

    /**
     * Add a record to the database to indicate that a particular
     * RDF dataset has been loaded
     *
     * @param Biotaerdf\Model\BioteaRdfSet $rdfSet
     */
    public function markLoaded(BioteaRdfSet $rdfSet)
    {
        //this->conn->insert()
    }

    // --------------------------------------------------------------

    /**
     * Check to see if a particular RDF set has been loaded already  
     *
     * @param Biotaerdf\Model\BioteaRdfSet $rdfSet
     * @return boolean  TRUE if loaded; FALSE if not yet
     */
    public function isAlreadyLoaded(BioteaRdfSet $rdfSet)
    {
        //this->conn->query on MD5
    }

    // --------------------------------------------------------------

    public function isSetUp()
    {
        //get schema manager ...
    }

    // --------------------------------------------------------------    

    public function setUp()
    {
        //get schema manager ...
    }
}
/* EOF: BioteaRdfSetTracker.php */