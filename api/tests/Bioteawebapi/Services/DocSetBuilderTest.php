<?php

namespace Bioteawebapi\Services;

class DocSetBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateAsObjectSucceeds()
    {
        $obj = $this->getObj();
        $this->assertInstanceOf('\\Bioteawebapi\Services\DocSetBuilder', $obj);
    }

    // --------------------------------------------------------------

    public function testStuff()
    {
        $obj = $this->getObj();
        $tr = $obj->getTraverser($this->getTestPath());

        while ($item = $tr->getNextDocument()) {
            echo $item->getMainFilePath() . "\n";
            ob_flush();
        }
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