<?php

namespace Bioteardf\MiscTool;

use Bioteardf\Service\BioteaRdfSetTracker;
use Bioteardf\Service\MiscDataStore;
use BioteaRdf\Model\BioteaRdfSet;
use SimpleXMLElement;

class MaterialsAndMethods
{
    /**
     * @var Bioteardf\Service\BioteaRdfSetTracker
     */
    private $fileTracker;

    /**
     * @var Bioteardf\Service\MiscDataStore
     */
    private $dataStore;

    // --------------------------------------------------------------

    /**
     * Constructor
     */
    public function __construct(BioteaRdfSetTracker $fileTracker, MiscDataStore $dataStore)
    {
        $this->fileTracker = $fileTracker;
        $this->dataStore   = $dataStore;
    }

    // --------------------------------------------------------------

    public function analyze(BioteaRdfSet $set)
    {
        //is it in the database already?  skip due to already analyzed

        //Is there a materials and methods section?

        //If not, skip due to no m&m section

        //if so, do the analysis
    }

    // --------------------------------------------------------------

    private function doAnalysis(BioteaRdfSet $set)
    {
        foreach($set->annotationFiles as $annotFile)
        {

        }
    }

    // --------------------------------------------------------------

    private function extractData(SimpleXMLElement $annotFileXml)
    {


        
    }

}
/* EOF: MaterialsAndMethods.php */