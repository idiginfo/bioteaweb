<?php

namespace Bioteardf\Service\Indexes\Report;

class NumDocsPerJournalWithMM
{

    public function getDescription()
    {
        return 'Number of documents that have a Materials and Methods section grouped by Journal';
    }

    // --------------------------------------------------------------

    public function getSQL()
    {
        $str = "SELECT Journal.name, COUNT(DISTINCT Document.pmid) AS numDocs
            FROM Journal LEFT JOIN  
                (Document INNER JOIN Paragraph ON Paragraph.document_id = Document.id)
            ON Document.journal_id = Journal.id
            WHERE Paragraph.identifier LIKE '%/methods/%' OR Paragraph.identifier LIKE '%/materials/%' OR Paragraph.identifier LIKE '%/materials-and-methods/%' OR Paragraph.identifier LIKE '%/materials_and_methods/%'
            GROUP BY Journal.name;";

        return $str;
    }
}

/* EOF: NumDocsPerJournalWithMM.php */