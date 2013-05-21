<?php

namespace Bioteardf\Service\Indexes;

use Bioteardf\Exception\BioteaRdfParseException;
use SimpleXMLElement, Exception, SplFileInfo;

/**
 * Parser Class
 */
abstract class RdfFileParser
{
    public function parseFile(SplFileInfo $file, DocObjectRegistry $docReg)
    {
        try {
            $xml = @new SimpleXMLElement((string) $file, 0, true);
            $this->parse($xml, $docReg);
        }
        catch (Exception $e) {
            throw new BioteaRdfParseException("Could not extract XML from file: " . (string) $file);            
        }        
    }

    // --------------------------------------------------------------

    public function parseRaw($xmlString, DocObjectRegistry $docReg)
    {
        try {
            $xml = @new SimpleXMLElement($xmlString);
            $this->parse($xml, $docReg);
        }
        catch (Exception $e) {
            throw new BioteaRdfParseException("Could not extract XML");            
        }
    } 

    // --------------------------------------------------------------

    abstract public function parse(SimpleXMLElement $xml, DocObjectRegistry $docReg);
}

/* EOF: ParserInterface.php */