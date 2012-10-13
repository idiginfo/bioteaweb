<?php

namespace Bioteawebapi\Services;

class IndexBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testTestPathIsReadable()
    {
        $this->assertTrue(is_readable($this->getTestPath()));
    }
    
    // --------------------------------------------------------------

    public function testInstantiateAsObjectSucceeds()
    {
        $obj = $this->getObj();
        $this->assertInstanceOf('\Bioteawebapi\Services\IndexBuilder', $obj);
    }

    // --------------------------------------------------------------

    public function testBuildIndexBuildsAValidObjectForValidPath()
    {
        //Get a file from the fixtures
        $fullPath = realpath($this->getTestPath() . '/PMC1134665.rdf');
        $relPath  = basename($fullPath);

        //Ensure result works
        $document = $this->getObj()->buildDocument($fullPath, $relPath);

        $this->assertInstanceOf('\Bioteawebapi\Entities\Document', $document);
        $this->assertEquals($relPath, $document->getRdfFilePath());

        //Check annotation filepaths
        $aoArray = array(
            'ncbo'     => 'AO_annotations/PMC1134665_ncboAnnotator.rdf', 
            'whatizit' => 'Bio2RDF/PMC1134665_whatizitUkPmcAll.rdf'
        );

        $this->assertEquals($aoArray, $document->getRDFAnnotationPaths());
    }

    // --------------------------------------------------------------

    public function testBuildDocumentThrowsExceptionForInvalidPath()
    {
        $this->setExpectedException('\Bioteawebapi\Exceptions\IndexBuilderException');

        //Get non-existent filepath in fixtures
        $fullPath = realpath($this->getTestPath() . '/PMC999999998888888887654321.rdf');
        $relPath  = basename($fullPath);   

        $docset = $this->getObj()->buildDocument($fullPath, $relPath);
    }

    // --------------------------------------------------------------

    public function testGetTraverserReturnsATraverserForAValidFilePath()
    {
        $obj = $this->getObj();
        $tr = $obj->getTraverser($this->getTestPath());
        $this->assertInstanceOf('\Bioteawebapi\Services\IndexBuilder', $tr);
    }

    // --------------------------------------------------------------

    public function testGetTraverserThrowsExceptionForInvalidFilePath()
    {
        $this->setExpectedException('\InvalidArgumentException');

        $obj = $this->getObj();
        $tr = $obj->getTraverser("/really/does/not/exist/yo");
        $this->assertInstanceOf('\Bioteawebapi\Services\Index', $tr);        
    }

    // --------------------------------------------------------------

    public function testTraverserReturnsObjects()
    {
        $obj = $this->getObj();
        $tr = $obj->getTraverser($this->getTestPath());

        for ($i = 0; $i < 3; $i++) {
            $item = $tr->getNextDocument();
            $this->assertGreaterThan(0, strlen($item->getRdfFilePath()));
        }
    }

    // --------------------------------------------------------------

    public function testTraverserNotAvailableThruInstantiatedObject()
    {
        $this->setExpectedException("\Exception");

        $obj = $this->getObj();
        $obj->getNextDocument();
    }

    // --------------------------------------------------------------

    /**
     * Get the path to the test data
     *
     * @return string
     */
    protected function getTestPath()
    {
        return __DIR__ . '/../../fixtures/rdfSampleFolder';
    }

    // --------------------------------------------------------------

    /**
     * Get a Index object for testing
     *
     * @param boolean|array $vocabs  If true, read the vocabs from the fixture file
     *                               If array, use the supplied vocabs
     *                               If false, don't use vocabs
     * @return Index
     */
    protected function getObj($vocabs = true)
    {
        if ($vocabs === true) {
            include(__DIR__ . '/../../fixtures/vocabularies.php');
        }
        elseif ($vocabs === false) {
            $vocabs = array();
        }

        return new IndexBuilder($vocabs);
    }

}

/* EOF: IndexTest.php */