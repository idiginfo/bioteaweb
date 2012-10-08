<?php

namespace Bioteawebapi\Services;

class DocSetBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testTestPathIsReadable()
    {
        $this->assertTrue(is_readable($this->getTestPath()));
    }
    
    // --------------------------------------------------------------

    public function testInstantiateAsObjectSucceeds()
    {
        $obj = $this->getObj();
        $this->assertInstanceOf('\Bioteawebapi\Services\DocSetBuilder', $obj);
    }

    // --------------------------------------------------------------

    public function testBuildDocSetBuildsAValidObjectForValidPath()
    {
        //Get a file from the fixtures
        $fullPath = realpath($this->getTestPath() . '/PMC1134665.rdf');
        $relPath  = basename($fullPath);

        //Ensure result works
        $docset = $this->getObj()->buildDocSet($fullPath, $relPath);

        $this->assertInstanceOf('\Bioteawebapi\Models\BioteaDocSet', $docset);
        $this->assertEquals($relPath, $docset->getMainFilePath());

        //Check annotation filepaths
        $aoArray = array('AO_annotations/PMC1134665_ncboAnnotator.rdf', 'AO_annotations/PMC1134665_whatizitUkPmcAll.rdf');
        $this->assertEquals($aoArray, $docset->getAnnotationFilePaths());
    }

    // --------------------------------------------------------------

    public function testBuildDocSetThrowsExceptionForInvalidPath()
    {
        $this->setExpectedException('\Bioteawebapi\Exceptions\DocSetBuilderException');

        //Get non-existent filepath in fixtures
        $fullPath = realpath($this->getTestPath() . '/PMC999999998888888887654321.rdf');
        $relPath  = basename($fullPath);   

        $docset = $this->getObj()->buildDocSet($fullPath, $relPath);
    }

    // --------------------------------------------------------------

    public function testGetTraverserReturnsATraverserForAValidFilePath()
    {
        $obj = $this->getObj();
        $tr = $obj->getTraverser($this->getTestPath());
        $this->assertInstanceOf('\Bioteawebapi\Services\DocSetBuilder', $tr);
    }

    // --------------------------------------------------------------

    public function testGetTraverserThrowsExceptionForInvalidFilePath()
    {
        $this->setExpectedException('\InvalidArgumentException');

        $obj = $this->getObj();
        $tr = $obj->getTraverser("/really/does/not/exist/yo");
        $this->assertInstanceOf('\Bioteawebapi\Services\DocSetBuilder', $tr);        
    }

    // --------------------------------------------------------------

    public function testTraverserReturnsObjects()
    {
        $obj = $this->getObj();
        $tr = $obj->getTraverser($this->getTestPath());

        for ($i = 0; $i < 3; $i++) {
            $item = $tr->getNextDocument();
            $this->assertGreaterThan(0, strlen($item->getMainFilePath()));
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
        return __DIR__ . '/../../fixtures/solrIndexerTestData';
    }

    // --------------------------------------------------------------

    /**
     * Get a DocSetBuilder object for testing
     *
     * @param boolean|array $vocabs  If true, read the vocabs from the fixture file
     *                               If array, use the supplied vocabs
     *                               If false, don't use vocabs
     * @return DocSetBuilder
     */
    protected function getObj($vocabs = true)
    {
        if ($vocabs === true) {
            include(__DIR__ . '/../../fixtures/vocabularies.php');
        }
        elseif ($vocabs === false) {
            $vocabs = array();
        }

        return new DocSetBuilder($vocabs);
    }

}

/* EOF: DocSetBuilderTest.php */