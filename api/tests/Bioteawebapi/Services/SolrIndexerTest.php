<?php

namespace Bioteawebapi\Services;

class SolrIndexerTest extends \PHPUnit_Framework_TestCase
{

    // --------------------------------------------------------------

    public function testTestPathIsReadable()
    {
        $this->assertTrue(is_readable($this->getTestPath()));
    }

    // --------------------------------------------------------------

    public function testInstantiateAsObjectSucceeds()
    {
        $this->assertInstanceOf('\Bioteawebapi\Services\SolrIndexer', $this->getObj());
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

    public function testIndex()
    {
        $obj = $this->getObj();
        $obj->index();     
    }

    // --------------------------------------------------------------

    /**
     * Get the object to test (with mocks injected)
     *
     * @return \Bioteawebapi\Services\SolrIndexer
     */
    protected function getObj($path = null, $solrClient = null)
    {
        $path = $path ?: $this->getTestPath();
        $solr = $solrClient ?: $this->getSolrClientMock();

        return new SolrIndexer($path, $solr);
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
}

/* EOF: SolrIndexerTest.php */