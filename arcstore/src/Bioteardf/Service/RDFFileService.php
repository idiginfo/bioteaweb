<?php

namespace Bioteardf\Service;

use Bioteardf\Helper\BioteaRdfSetIterator;
use RecursiveDirectoryIterator as RDI;
use RecursiveIteratorIterator;
use SplFileInfo;
use ArrayIterator;
use RuntimeException;

class RDFFileService
{
    /**
     * Return an iterator wether the filepath is a directory or a single file
     *
     * @return Iterator of SPLFileInfo Objects
     */
    public function getIterator($filepath)
    {
        if ( ! is_readable($filepath)) {
            throw new RuntimeException("Could not read directory or file: " . $filepath);
        }

        return (is_dir($filepath))
            ? new BioteaRdfSetIterator(new RecursiveIteratorIterator(new RDI($filepath)))
            : new ArrayIterator(array(new SplFileInfo($filepath)));
    }
}

/* EOF: FileService.php */