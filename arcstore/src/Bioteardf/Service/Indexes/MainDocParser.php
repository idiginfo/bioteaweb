<?php

namespace Bioteardf\Service\Indexes;

use Bioteardf\Exception\BioteaRdfParseException;
use SimpleXMLElement;

/**
 * Main Document Parser
 */
class MainDocParser extends RdfFileParser
{
    public function parse(SimpleXMLElement $xml, DocObjectRegistry $docReg)
    {
        $docObj = $docReg->getDocObj();

        //Find the journal name (first journal listed)
        $journalName = $xml->xpath('//rdf:Description//rdf:type[@rdf:resource = "http://purl.org/ontology/bibo/Journal"][1]/following-sibling::dcterms:publisher');
        $journalName = (string) $journalName[0];
        $docObj->setJournal($docReg->getObj('Journal', $journalName));

        //Get all the paragraphs
        $pgrhXpath = '//rdf:Description//rdf:type[@rdf:resource="http://www.w3.org/2011/content#Content"]/parent::rdf:Description';
        foreach($xml->xpath($pgrhXpath) as $pgrh) {
            $pgrhName    = (string) $pgrh->attributes('rdf', TRUE)->about;
            $pgrhContent = utf8_encode((string) current($pgrh->xpath('cnt:chars')));

            $pgrphObj = $docReg->getObj('Paragraph', array($pgrhName, $pgrhContent, $docObj));
            $docObj->addParagraph($pgrphObj);
        }     
    }
}

/* EOF: MainDocParser.php */