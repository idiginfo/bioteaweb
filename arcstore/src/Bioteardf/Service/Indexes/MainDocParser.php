<?php

namespace Bioteardf\Service\Indexes;

use Bioteardf\Exception\BioteaRdfParseException;
use Bioteardf\Model\Doc;
use SimpleXMLElement;

/**
 *
 */
class MainDocParser extends RdfFileParser
{
    public function parse(SimpleXMLElement $xml, Doc\Document $doc)
    {
        //Find the journal name (first journal listed)
        $journalName = $xml->xpath('//rdf:Description//rdf:type[@rdf:resource = "http://purl.org/ontology/bibo/Journal"][1]/following-sibling::dcterms:publisher');
        $journalName = (string) $journalName[0];
        $doc->setJournal(new Doc\Journal($journalName));

        //Get all the paragraphs
        $pgrhXpath = '//rdf:Description//rdf:type[@rdf:resource="http://www.w3.org/2011/content#Content"]/parent::rdf:Description';
        foreach($xml->xpath($pgrhXpath) as $pgrh) {
            $pgrhName    = (string) $pgrh->attributes('rdf', TRUE)->about;
            $pgrhContent = utf8_encode((string) current($pgrh->xpath('cnt:chars')));

            $doc->addParagraph(new Doc\Paragraph($pgrhName, $pgrhContent));
        }     
    }
}

/* EOF: MainDocParser.php */