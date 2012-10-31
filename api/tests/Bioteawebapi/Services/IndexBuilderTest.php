<?php

namespace Bioteawebapi\Services\Indexer;

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
        $this->assertInstanceOf('\Bioteawebapi\Services\Indexer\IndexBuilder', $obj);
    }

    // --------------------------------------------------------------

    public function testBuildIndexBuildsAValidObjectForValidPath()
    {
        //Get a file from the fixtures
        $fullPath = realpath($this->getTestPath() . '/PMC1134665.rdf');
        $relPath  = basename($fullPath);

        //Ensure result works
        $document = $this->getObj()->buildDocument($relPath);

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
        //$this->setExpectedException('\Bioteawebapi\Exceptions\IndexBuilderException');

        //Get non-existent filepath in fixtures
        $fullPath = realpath($this->getTestPath() . '/PMC99999999999.rdf');
        $relPath  = basename($fullPath);

        $docset = $this->getObj()->buildDocument($relPath);
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
        //Use the real filesObj
        $filesObj = new \Bioteawebapi\Services\RDFFileClient($this->getTestPath(), 'http://localhost/test/');

        //Vocabs
        if ($vocabs === true) {
            include(__DIR__ . '/../../fixtures/vocabularies.php');
        }
        elseif ($vocabs === false) {
            $vocabs = array();
        }

        return new IndexBuilder($filesObj, $vocabs);
    }

}

/* EOF: IndexTest.php */