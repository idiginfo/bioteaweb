--List of Paragraphs which are M&M
SELECT Document.pmid, Paragraph.id, Paragraph.identifier
FROM Paragraph LEFT JOIN Document ON Document.id = Paragraph.document_id

--Number M&M Paragraphs per Document (only docs with M&M)
SELECT Document.pmid, COUNT(Paragraph.id) AS numMMParagraphs
FROM Paragraph LEFT JOIN Document ON Document.id = Paragraph.document_id
WHERE Paragraph.identifier LIKE '%methods%' OR Paragraph.identifier LIKE '%materials%'
GROUP BY Document.pmid;

--Number of Documents Per Journal
SELECT Journal.name, COUNT(Document.id)
FROM Journal LEFT JOIN Document ON Document.journal_id = Journal.id
GROUP BY Journal.name;

--Number of Documents where M&M Exists Per Journal
SELECT Journal.name, COUNT(DISTINCT Document.pmid) AS numDocs
FROM Journal LEFT JOIN  
    (Document INNER JOIN Paragraph ON Paragraph.document_id = Document.id)
ON Document.journal_id = Journal.id
WHERE Paragraph.identifier LIKE '%/methods/%' OR Paragraph.identifier LIKE '%/materials/%' OR Paragraph.identifier LIKE '%/materials-and-methods/%' OR Paragraph.identifier LIKE '%/materials_and_methods/%'
GROUP BY Journal.name;

--Terms and their number of instances
SELECT Term.term, COUNT(TermInstance.id) AS numInstances
FROM Term LEFT JOIN
    (Annotation LEFT JOIN TermInstance ON TermInstance.annotation_id = Annotation.id)
ON Annotation.term_id = Term.id
GROUP BY Term.term;

--Terms and their number of instances in M&M only
SELECT Term.term, COUNT(TermInstance.id) AS numInstances
FROM Term LEFT JOIN
    (Annotation LEFT JOIN 
        (TermInstance LEFT JOIN Paragraph ON TermInstance.paragraph_id = Paragraph.id)
    ON TermInstance.annotation_id = Annotation.id)
ON Annotation.term_id = Term.id
WHERE Paragraph.identifier LIKE '%/methods/%' OR Paragraph.identifier LIKE '%/materials/%' OR Paragraph.identifier LIKE '%/materials-and-methods/%' OR Paragraph.identifier LIKE '%/materials_and_methods/%'
GROUP BY Term.term;

--Number of topics per term
SELECT Term.term, COUNT(Topic.id) AS numTopics
FROM Term LEFT JOIN Topic ON Term.id = Topic.term_id
GROUP BY Term.term;