<?php

namespace Bioteawebapi\Services;

class RDFFIleClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $rdfPath;

    /**
     * @var string
     */
    private $baseUrl;

    // --------------------------------------------------------------

    public function setUp()
    {
        $this->rdfPath = realpath(__DIR__ . '/../../fixtures/rdfSampleFolder');
        $this->baseUrl = 'http://localhost/biotawebapitest';
    }

    // --------------------------------------------------------------

    public function testInstantiateAsObjectSucceeds()
    {
        $obj = new RDFFileClient($this->rdfPath, $this->baseUrl);
        $this->assertInstanceOf('\Bioteawebapi\Services\RDFFileClient', $obj);
    }

    // --------------------------------------------------------------

    public function testNonExistentPathThrowsException()
    {
        $this->setExpectedException("\RuntimeException");
        $obj = new RDFFileClient('/totally/doesnt/exist/yo', $this->baseUrl);
    }

    // --------------------------------------------------------------

    public function testResolvePathReturnsExpectedResult()
    {
        $obj = new RDFFileClient($this->rdfPath, $this->baseUrl);    
        $result   = $obj->resolvePath('somefile.rdf');
        $expected = $this->rdfPath . '/somefile.rdf';

        $this->assertEquals($expected, $result);
    }    

    // --------------------------------------------------------------

    public function testResolveUrlReturnsExpectedResult()
    {
        $obj = new RDFFileClient($this->rdfPath, $this->baseUrl);    
        $result   = $obj->resolveUrl('somefile.rdf');
        $expected = $this->baseUrl . '/somefile.rdf';

        $this->assertEquals($expected, $result);
    }

    // --------------------------------------------------------------

    public function testGetNumRdfFilesReturnsExpectedNumber()
    {
        $obj = new RDFFileClient($this->rdfPath, $this->baseUrl);    

        $this->assertEquals(100, $obj->countRdfFiles());
    }

    // --------------------------------------------------------------

    public function testIteratorWorks()
    {
        $obj = new RDFFileClient($this->rdfPath, $this->baseUrl);    

        $arr = array();
        while ($item = $obj->getNextFile()) {
            $arr[] = $item;
        }

        //@TODO: Why does the iterator only return 99 items?
        $this->assertEquals(99, count($arr));
        $this->assertContains('subFolder/PMC534113.rdf', $arr);
        $this->assertContains('PMC2763859.rdf', $arr);
    }

    // --------------------------------------------------------------

    public function testGetAnnotationPathsSucceeds()
    {
        $obj = new RDFFileClient($this->rdfPath, $this->baseUrl);    

        $realFile = 'subFolder/PMC534113.rdf';
        $apaths = $obj->getAnnotationFiles($realFile);

        foreach ($apaths as $file) {
            $this->assertFileExists($file);
        }
    }

    // --------------------------------------------------------------

    public function testGetRelativeAnnotationPathsSucceeds()
    {
        $obj = new RDFFileClient($this->rdfPath, $this->baseUrl);    

        $realFile = 'subFolder/PMC534113.rdf';
        $apaths = $obj->getAnnotationFiles($realFile, false);

        $expectedArray = array(
            'ncbo' => 'subFolder/AO_annotations/PMC534113_ncboAnnotator.rdf',
            'whatizit' => 'subFolder/Bio2RDF/PMC534113_whatizitUkPmcAll.rdf'
        );

        $this->assertEquals($expectedArray, $apaths);
    }
}

/* EOF: RDFFileClientTest.php */