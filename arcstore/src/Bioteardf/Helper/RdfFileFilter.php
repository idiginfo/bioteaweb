<?php

namespace Bioteardf\Helper;

use RecursiveFilterIterator,
    RecursiveIterator;

/**
 * Iterator Filter for getting only .rdf files
 * See: http://stackoverflow.com/questions/1860393/recursive-file-search-php
 */
class RdfFileFilter extends RecursiveFilterIterator
{
    public function __construct(RecursiveIterator $iterator)
    {
        parent::__construct($iterator);
    }

    public function accept()
    {
        if ($this->current()->isDir()) {
            return true;
        }
        else {
            return $this->current()->isFile() && preg_match("/\.rdf$/ui", $this->current()->getFilename());
        }
    }

    public function __toString()
    {
        return $this->current()->getFilename();
    }
}

/* EOF: RdfFileFilter.php */