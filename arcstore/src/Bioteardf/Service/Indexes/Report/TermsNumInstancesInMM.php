<?php

namespace Bioteardf\Service\Indexes\Report;

class TermsNumInstancesInMM
{
    public function getDescription()
    {
        return 'Number of Term Instances Per Term in Materials and Methods Sections';
    }

    // --------------------------------------------------------------

    public function getSQL()
    {
        $str = "SELECT Term.term, COUNT(TermInstance.id) AS numInstances
            FROM Term LEFT JOIN
                (Annotation LEFT JOIN 
                    (TermInstance LEFT JOIN Paragraph ON TermInstance.paragraph_id = Paragraph.id)
                ON TermInstance.annotation_id = Annotation.id)
            ON Annotation.term_id = Term.id
            WHERE Paragraph.identifier LIKE '%/methods/%' OR Paragraph.identifier LIKE '%/materials/%' OR Paragraph.identifier LIKE '%/materials-and-methods/%' OR Paragraph.identifier LIKE '%/materials_and_methods/%'
            GROUP BY Term.term;";

        return $str;
    }
}

/* EOF: TermsNumInstancesInMM.php */