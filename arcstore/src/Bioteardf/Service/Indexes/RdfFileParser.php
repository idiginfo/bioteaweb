<?php

namespace Bioteardf\Service\Indexes;

use Bioteardf\Exception\BioteaRdfParseException;
use Bioteardf\Model\Doc;
use SimpleXMLElement, Exception, SplFileInfo;

/**
 * Parser Class
 */
abstract class RdfFileParser
{
    public function parseFile(SplFileInfo $file, Doc\Document $doc)
    {
        try {
            $xml = @new SimpleXMLElement((string) $file, 0, true);
            $docObj = $this->parse($xml, $doc);
        }
        catch (Exception $e) {
            throw new BioteaRdfParseException("Could not extract XML from file: " . (string) $file);            
        }        
    }

    // --------------------------------------------------------------

    public function parseRaw($xmlString, Doc\Document $doc)
    {
        try {
            $xml = @new SimpleXMLElement($xmlString);
            $docObj = $this->parse($xml, $doc);
        }
        catch (Exception $e) {
            throw new BioteaRdfParseException("Could not extract XML");            
        }
    } 

    // --------------------------------------------------------------

    abstract public function parse(SimpleXMLElement $xml, Doc\Document $doc);
}

/* EOF: ParserInterface.php */