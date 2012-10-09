<?php

namespace Bioteawebapi\Services;

class IndexerTest extends \PHPUnit_Framework_TestCase
{
    public function testTestPathIsReadable()
    {
        $this->assertTrue(is_readable($this->getTestPath()));
    }
    
    // --------------------------------------------------------------
   
    public function testInstantiateAsObjectSucceeds()
    {
        $this->assertInstanceOf('\Bioteawebapi\Services\Indexer', $this->getObj());
    }

    // --------------------------------------------------------------

    public function testGetNumProcessedReturnsZeroBeforeExecution()
    {
        $obj = $this->getObj();
        $this->assertEquals(0, $obj->getNumProcessed());
    }

    // --------------------------------------------------------------

    /**
     * Here we use a limit so as to not take a million years for the
     * test to run
     */
    public function testIndexWorksForValidPath()
    {
        $obj = $this->getObj();
        $obj->index($this->getTestPath(), 3);
        $this->assertEquals(3, $obj->getNumProcessed());
    }

    // --------------------------------------------------------------

    public function testIndexThrowsExceptionForNonExistentPath()
    {
        $this->setExpectedException("\InvalidArgumentException");

        $obj = $this->getObj();
        $obj->index('/really/doesnt/exist/again/asdfa/a' . time(), 3);
    }

    // --------------------------------------------------------------

    /**
     * Get the object to test (with mocks injected)
     *
     * @return \Bioteawebapi\Services\SolrIndexer
     */
    protected function getObj($path = null, $solrClient = null)
    {
        $builder = new DocSetBuilder();
        $solr    = $solrClient ?: $this->getSolrClientMock();
        $mysql   = $this->getMySQLClientMock();

        return new Indexer($builder, $mysql);
    }

    // --------------------------------------------------------------

    /**
     * Get a mock object for the SOLR Client
     *
     * @return Bioteawebapi\Services\SolrClient
     */
    protected function getSolrClientMock()
    {
        $mock = $this->getMock('\Bioteawebapi\services\SolrClient', array('update', 'commit'), array(), '', false);
        $mock->expects($this->any())->method('update')->will($this->returnValue(1));
        $mock->expects($this->any())->method('commit')->will($this->returnValue(1));
        return $mock;
    }

    // --------------------------------------------------------------

    protected function getMySQLClientMock()
    {
        $mock = $this->getMock('\Bioteawebapi\services\MySQLClient', array(), array(), '', false);
        return $mock;
    }   

    // --------------------------------------------------------------

    /**
     * Get the path to the test data
     *
     * @return string
     */
    protected function getTestPath()
    {
        return __DIR__ . '/../../fixtures/solrIndexerTestData';
    }
}

/* EOF: SolrIndexerTest.php */