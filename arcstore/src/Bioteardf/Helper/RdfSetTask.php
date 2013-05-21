<?php

namespace Bioteardf\Helper;

use RuntimeException, SplFileInfo;
use Bioteardf\Model\BioteaRdfSet;

/**
 * RDF Set Task has a few helper functions
 */
abstract class RdfSetTask
{
    protected function deserializeRdfSet($jsonData)
    {
        $data = json_decode($jsonData);
        $mainFile   = new SplFileInfo($data->mainFile);
        $annotFiles = array_map(
            function($fp) {
                return new SplFileInfo($fp);
            },
            $data->annotationFiles
        );

        return new BioteaRdfSet($mainFile, $annotFiles);
    }
}
/* EOF: RdfSetTask.php */