<?php

namespace Bioteawebapi\Services;

class IndexerTest extends \PHPUnit_Framework_TestCase
{

    // --------------------------------------------------------------

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

    public function testInvalidPathThrowsInvalidArgumentException()
    {
        $this->setExpectedException("\InvalidArgumentException");
        $obj = $this->getObj('/does/not/exist');
    }

    // --------------------------------------------------------------

    public function testGetNumIndexedReturnsZeroBeforeExecution()
    {
        $obj = $this->getObj();
        $this->assertEquals(0, $obj->getNumIndexed());
    }

    // --------------------------------------------------------------

    //Playing with this!!
    public function testIndex()
    {
        include(__DIR__ . '/../../fixtures/vocabularies.php');

        $obj = $this->getObj();
        $obj->setVocabularies($vocabs);
        $obj->index(1);     
    }

    // --------------------------------------------------------------

    /**
     * Get the object to test (with mocks injected)
     *
     * @return \Bioteawebapi\Services\SolrIndexer
     */
    protected function getObj($path = null, $solrClient = null)
    {
        $path  = $path ?: $this->getTestPath();
        $solr  = $solrClient ?: $this->getSolrClientMock();
        $mysql = $this->getMySQLClientMock();

        return new Indexer($path, $solr, $mysql);
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
}

/* EOF: SolrIndexerTest.php */